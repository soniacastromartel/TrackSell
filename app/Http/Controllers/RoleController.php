<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Auth;


class RoleController extends Controller
{

    public function __construct()
    {
        $this->title = 'Roles'; 
        $this->user = session()->get('user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if ($request->ajax()) {

                $roles = Role::select('roles.id','roles.name','roles.description'); 
    
                return DataTables::of($roles)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        
                        if (!empty($request->get('search'))) {
                                $instance-> where (function ($w) use ($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('roles.description', 'LIKE', "%$search%")
                                        ->orWhere('roles.name', 'LIKE', "%$search%");
                                } );
                        }
                    })
                    ->addColumn('action', function($role){
                        $btn = '';
                        $fnCall = 'destroy(\'' . $role->id . '\')';

                        $btn .= '<a href="roles/edit/'.$role->id.'"class="btn-edit tooltip-edit" style=""><span class="material-symbols-outlined">
                        edit</span></a>';

                        $btn .= '<a onclick="confirmRequest(0,' . $role->id . ')" class="btn-delete tooltip-remove"><span class="material-symbols-outlined">
                        delete</span></a>';
                        
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.roles.index', ['title' => $this->title]);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar roles, contacte con el administrador');
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::getRolesActive(); 
        return view('admin.roles.create', ['title' => $this->title, 'roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request-> validate([
            'name'       => 'required',
        ]);
        try{
            $role_id = Role::create($request->all())->id; 
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->action('RoleController@index')->with('error', 'Ha ocurrido un error al crear rol, contacte con el administrador');
        }
        return redirect()->action('RoleController@index')
    
                            ->with('success','Role creado con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $role = Role::find($id);
            $roles = Role::getRolesActive(); 
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->action('RoleController@index')->with('error', 'Ha ocurrido un error al cargar rol para editar, contacte con el administrador');
        }    

        return view('admin.roles.edit', ['title' => $this->title, 'role' => $role, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()){
                return redirect()->action('RoleController@index')
                ->with('error','Ha ocurrido un error');

            } else {

                $role = Role::find($id);
                $params = $request->all(); 
                $role->update($params);

                return redirect()->action('RoleController@index')
        
                                ->with('success','Rol actualizado correctamente');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->action('RoleController@index')->with('error', 'Ha ocurrido un error al actualizar rol, contacte con el administrador');
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);
            $params = $request->all();
            $id = $params['id'];
            $role = Role::findOrFail($id);
            $role->update(['cancellation_date' => now()]);
            return response()->json([
                'success' => true,
                'mensaje' => 'Rol eliminado correctamente',
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Ha ocurrido un error al eliminar el rol, contacte con el administrador.',
            ], 500); // 500 es un error de servidor interno
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Ocurrió un error inesperado.',
            ], 500);
        }
    }

}