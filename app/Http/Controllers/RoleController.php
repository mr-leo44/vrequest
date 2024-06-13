<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('role.index',compact('roles'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,User $user)
    {
        
        $role = Role::create(['name'=>$request->name]);
        
        return back()->with('status','Enregistrement reussi');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    
        $role = Role::find($id);
       
        $rolename = $role->name;

    
        $permissions = Permission::whereHas('users', function ($query) use($rolename){
            $query->whereHas('roles', function ($q) use($rolename) {
                
                $q->where('name', $rolename);
            });
        })->get();
        
       
        
       
    
        $modelFiles = scandir(app_path('Models'));

        // Filtrer les fichiers de modèles
        $models = array_diff($modelFiles, ['.', '..']);
        
        // Supprimer l'extension .php des noms de fichiers
        $models = array_map(function($model) {
            return pathinfo($model, PATHINFO_FILENAME);
        }, $models);
        $modelname = [];
        foreach($models as $model) {
            $modelname[] = $model;
        }
      
        $tab = [];
       
        
        $per = ["lire","enregistrer","modifier","supprimer"];
        $per_vehicule = ["lire_vehicule","enregistrer_vehicule","modifier_vehicule","supprimer_vehicule"];
        $roleId=$role->id;
  
        
        return view('role.detail',compact('modelname','permissions','per','tab','roleId','per_vehicule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();
        return back()->with('status', 'Modification reussi');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        $role->delete();
        return back()->with('status','Suppression reussie');
    }
    
      
    public function assignRoles($role,$user){
        
        /*$user = auth()->user();
    
        $assignRole =  $user->assignRole('agent');
        */
        $users = User::find($user);

       $users->assignRole($role);
       return back()->with('status','rôle attribué avec succès');
    }
    public function desactiverRoles($role,$user){
        $users = User::find($user);

        $users->removeRole($role);
        return back()->with('status','rôle desactivé avec succès');
    }
    
}