<?php

namespace App\Http\Controllers\Authorization;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use App\Models\FrontendRolePermission;
use Illuminate\Support\Facades\DB;
use App\Models\User;


class AuthorizationController extends Controller
{
    public function get_unique_module_list()
    {
        try {
            $data = [];

            $data['module_list'] = Module::orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'message' => "Module List",
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function create_role_and_create_permission(Request $request)
    {
        //dd($request->all());

        DB::beginTransaction();

        try {
            $role_name = strtolower($request->role);

            $role = Role::where('name', $role_name)->where('guard_name', 'user')->first();

            if (!$role) {
                $role = Role::create(['guard_name' => 'user', 'name' => $role_name]);

                $role_id = $role->id;
                $permissions = $request->permissions;
                $is_storing_to_frontend_role_permission = false;
                foreach ($permissions as $permission) {
                    foreach ($permission as $key => $val) {

                        if ($key == 'module_id') {
                            $module = Module::find($val);
                        }

                        if ($module) {
                            $module_name = $module->name;
                        }

                        if ($key != 'module_id' && $val == true) {
                            $is_storing_to_frontend_role_permission = true;
                            $permission_name = $module_name . '_' . $key;

                            $permission = Permission::where('guard_name', 'user')
                                ->where('name', $permission_name)
                                ->first();

                            if (!$permission) {
                                $permission = Permission::create(['guard_name' => 'user', 'name' => $permission_name]);
                            }

                            $role->givePermissionTo($permission);
                        }

                    }
                }
                if ($is_storing_to_frontend_role_permission) {
                    foreach ($permissions as $permission) {

                        if ($permission['insert'] || $permission['update'] || $permission['view'] || $permission['delete'] || $permission['manage']) {
                            FrontendRolePermission::create(['role_id' => $role_id,
                                'module_id' => $permission['module_id'],
                                'insert_' => $permission['insert'],
                                'update_' => $permission['update'],
                                'view_' => $permission['view'],
                                'delete_' => $permission['delete'],
                                'manage_' => $permission['manage'],
                            ]);
                        }
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'To Create Role Or Permission Must Select Minimum One Permission.',
                    ], 200);
                }


                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Role & Permission are Created Successfully.",
                ], 200);

            } else {

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'There is Already a Role With this Role Name ' . $role_name,
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function create_role_driver_and_assign_that_role_driver_to_the_created_driver($user_id = null)
    {
        //dd($request->all());

        DB::beginTransaction();

        try {

            $role_name = 'driver';
            $role = Role::where('name', $role_name)->where('guard_name', 'user')->first();

            if (!$role) {
                $role = Role::create(['guard_name' => 'user', 'name' => $role_name]);
            }

            $user = User::find($user_id);

            if ($user) {

                $user->syncRoles([]);

                $user->assignRole($role);

                $user->remarks = 'Remarks';

                $user->save();

                DB::commit();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any User With User Id " . $user_id,
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function get_role_and_permission_list()
    {
        try {
            $data = [];

            $roles = Role::where('guard_name', 'user')
                ->orderBy('name', 'asc')
                ->select('id', 'name')
                ->get();

            foreach ($roles as $role) {
                $role_id = $role->id;
                $role_name = $role->name;
                $permissions = $role->permissions->pluck('name');
                $permissions_array = [];

                foreach ($permissions as $permission) {
                    array_push($permissions_array, $permission);
                }

                sort($permissions_array);

                $data[] = [
                    'role_id' => $role_id,
                    'role_name' => $role_name,
                    'permissions' => $permissions_array
                ];

            }

            return response()->json([
                'success' => true,
                'message' => "Role and Permission List",
                'data' => $data
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function delete_role(Request $request)
    {

        DB::beginTransaction();

        try {

            $validator = Validator::make($request->all(), [
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 200);
            }

            $role_id = $request->role_id;

            $role = Role::find($role_id);

            if ($role) {

                $permissions = $role->permissions;

                foreach ($permissions as $permission) {
                    $permission->removeRole($role);
                }

                FrontendRolePermission::where('role_id', $role_id)->delete();

                $role->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => "Role is Successfully Deleted.",
                ], 200);

            } else {

                return response()->json([
                    'success' => false,
                    'message' => "Do Not Find Any Role With Id " . $role_id,
                ], 200);

            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function assign_role_to_user(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'role_id' => 'required',
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 200);
            }

            $remarks = $request->remarks;

            $role_id = $request->role_id;

            $user_id = $request->user_id;

            $user = User::find($user_id);

            if ($user) {

                $user->syncRoles([]);

                $role = Role::find($role_id);

                if ($role) {

                    $user->assignRole($role);

                    $user->remarks = $remarks;

                    $user->save();

                    return response()->json([
                        'success' => true,
                        'message' => "Role is Assigned to User",
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Not Find Any Role With Role Id " . $role_id,
                    ], 200);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any User With User Id " . $user_id,
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function get_permission_list_of_authenticate_user()
    {
        try {

            $data = [];

            $user = auth('user')->user();

            $permissions = $user->getPermissionsViaRoles();

            $permissions_arr = [];

            foreach ($permissions as $permission) {
                $permissions_arr[] = $permission->name;
            }

            $data['permissions'] = $permissions_arr;

            return response()->json([
                'success' => true,
                'message' => "Permission List of Authenticate User",
                'data' => $data
            ], 200);


        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function get_role_and_permission_list_for_edit(Request $request)
    {
        try {

            $data = [];

            $role_id = $request->role_id;

            $frontend_role_permission = FrontendRolePermission::where('role_id', $role_id)->first();

            if ($frontend_role_permission) {

                $role = $frontend_role_permission->role;

                $role_id = $role->id;

                $role_name = $role->name;

                $data['role_id'] = $role_id;

                $data['role_name'] = $role_name;

            }

            $frontend_role_permissions = FrontendRolePermission::where('role_id', $role_id)->get();

            $permissions = [];

            foreach ($frontend_role_permissions as $frontend_role_permission) {
                $permission = [];
                $permission['module_id'] = $frontend_role_permission->module_id;

                $module = $frontend_role_permission->module;

                $module_name = $module->name;
                $permission['module_name'] = $module_name;


                $insert = $frontend_role_permission->insert_;
                if ($insert == 1) {
                    $insert = true;
                } elseif ($insert == 0) {
                    $insert = false;
                }
                $permission['insert'] = $insert;


                $view = $frontend_role_permission->view_;
                if ($view == 1) {
                    $view = true;
                } elseif ($view == 0) {
                    $view = false;
                }
                $permission['view'] = $view;


                $update = $frontend_role_permission->update_;
                if ($update == 1) {
                    $update = true;
                } elseif ($update == 0) {
                    $update = false;
                }
                $permission['update'] = $update;


                $delete = $frontend_role_permission->delete_;
                if ($delete == 1) {
                    $delete = true;
                } elseif ($delete == 0) {
                    $delete = false;
                }
                $permission['delete'] = $delete;


                $manage = $frontend_role_permission->manage_;
                if ($manage == 1) {
                    $manage = true;
                } elseif ($manage == 0) {
                    $manage = false;
                }
                $permission['manage'] = $manage;


                $permissions[] = $permission;
            }

            $data['permissions'] = $permissions;

            return response()->json([
                'success' => true,
                'message' => "Role and Permission List for Edit",
                'data' => $data
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function update_role_and_update_permission(Request $request)
    {
        DB::beginTransaction();

        try {

            $role_id = strtolower($request->role_id);
            $role_name = strtolower($request->role_name);

            $role = Role::find($role_id);

            if ($role) {
                $role_ = Role::where('name', $role_name)
                    ->where('guard_name', 'user')
                    ->where('id', '!=', $role_id)
                    ->first();

                if ($role_) {
                    return response()->json([
                        'success' => false,
                        'message' => 'There is Already a Role With this Role Name ' . $role_name,
                    ], 200);
                } else {
                    $role->name = $role_name;
                    $role->save();

                    $permissions = $request->permissions;

                    $permissions_unchecked = $permissions['unchecked'];

                    foreach ($permissions_unchecked as $permission_unchecked) {

                        $count_false_for_module = 0;

                        foreach ($permission_unchecked as $key => $val) {

                            if ($key == 'module_name') {
                                $module_name = $val;
                            }

                            if ($key != 'module_id' && $key != 'module_name' && $val == false) {

                                $count_false_for_module = $count_false_for_module + 1;

                                $permission_name = $module_name . '_' . $key;

                                $permission = Permission::where('guard_name', 'user')
                                    ->where('name', $permission_name)
                                    ->first();

                                if ($permission) {
                                    $role->revokePermissionTo($permission);
                                }

                            }

                        }

                        if ($count_false_for_module == 5) {
                            FrontendRolePermission::where('role_id', $role_id)
                                ->where('module_id', $permission_unchecked['module_id'])
                                ->delete();
                        } else {
                            return response()->json([
                                'success' => false,
                                'message' => 'Format of Input to This Request is Incorrect.'
                            ], 200);
                        }

                    }

                    $permissions_checked = $permissions['checked'];

                    foreach ($permissions_checked as $permission_checked) {

                        //  $count_false_for_module=0;

                        foreach ($permission_checked as $key => $val) {

                            if ($key == 'module_name') {
                                $module_name = $val;
                            }

                            if ($key != 'module_id' && $key != 'module_name' && $val == true) {

                                //   $count_false_for_module=$count_false_for_module+1;

                                $permission_name = $module_name . '_' . $key;

                                $permission = Permission::where('guard_name', 'user')
                                    ->where('name', $permission_name)
                                    ->first();

                                if (!$permission) {
                                    $permission = Permission::create(['guard_name' => 'user', 'name' => $permission_name]);
                                }

                                $role->givePermissionTo($permission);

                            } elseif ($key != 'module_id' && $key != 'module_name' && $val == false) {

                                //   $count_false_for_module=$count_false_for_module+1;

                                $permission_name = $module_name . '_' . $key;

                                $permission = Permission::where('guard_name', 'user')
                                    ->where('name', $permission_name)
                                    ->first();

                                if ($permission) {
                                    $role->revokePermissionTo($permission);
                                }

                            }

                        }

                        $frontend_role_permission = FrontendRolePermission::where('role_id', $role_id)
                            ->where('module_id', $permission_checked['module_id'])
                            ->first();

                        if ($frontend_role_permission) {
                            $frontend_role_permission->insert_ = $permission_checked['insert'];
                            $frontend_role_permission->view_ = $permission_checked['view'];
                            $frontend_role_permission->update_ = $permission_checked['update'];
                            $frontend_role_permission->delete_ = $permission_checked['delete'];
                            $frontend_role_permission->manage_ = $permission_checked['manage'];
                            $frontend_role_permission->save();
                        } else {
                            FrontendRolePermission::create([
                                'role_id' => $role_id,
                                'module_id' => $permission_checked['module_id'],
                                'insert_' => $permission_checked['insert'],
                                'view_' => $permission_checked['view'],
                                'update_' => $permission_checked['update'],
                                'delete_' => $permission_checked['delete'],
                                'manage_' => $permission_checked['manage']
                            ]);
                        }
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => "Role and Permission are Updated Successfully."
                    ], 200);

                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Do Not Find Any Role With The Role Id ' . $role_id
                ], 200);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function get_role_list()
    {
        try {
            $data = [];

            $roles = Role::where('guard_name', 'user')
                ->orderBy('name', 'asc')
                ->select('id', 'name')
                ->get();

            $data['roles'] = $roles;

            return response()->json([
                'success' => true,
                'message' => "Role List",
                'data' => $data
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function get_users_with_roles()
    {
        try {
            $data = [];

            $users = User::get();

            $cards = [];

            foreach ($users as $user) {
                if (count($user->getRoleNames()) > 0) {

                    $image = null;

                    if ($user->employee) {
                        $image = url('/') . '/' . $user->employee->image;
                    }

                    $cards[] = [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'image' => $image,
                        'role_list' => $user->getRoleNames()
                    ];
                }
            }

            $data['cards'] = $cards;

            return response()->json([
                'success' => true,
                'message' => "Users With Roles List",
                'data' => $data
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

    public function remove_role_from_user(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Validation Error.",
                    'error' => $validator->errors(),
                ], 200);
            }

            $user_id = $request->user_id;

            $user = User::find($user_id);

            if ($user) {

                $user->syncRoles([]);

                return response()->json([
                    'success' => true,
                    'message' => "Role is Removed From User",
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Not Find Any User With User Id " . $user_id,
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
        }

    }

}
