<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Centre;
use DataTables;
use DB;

class CentreController extends Controller
{

    public function __construct()
    {
        $this->title = 'Centros';
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
                $centres = DB::table('centres')
                 ->whereNull('cancellation_date')
                ->orderBy('name');

                // $centres = Centre::all();
                return DataTables::of($centres)
               
                    ->addIndexColumn()
                    ->addColumn('action', function($centre){
                        $buttons = '';
                        if (empty($centre->cancellation_date)) {
                            $buttons = '<a href="centres/edit/'.$centre->id.'" class="btn btn-warning a-btn-slide-text"><span class="material-icons">
                            edit
                            </span> Editar</a>';
                            $buttons .= '<a onclick="confirmRequest(0,' . $centre->id . ')" class="btn btn-red-icot a-btn-slide-text"><span class="material-icons">
                            delete
                            </span> Borrar</a>';
                        }    
                        return $buttons;
                    })
                    ->filter(function ($instance) use ($request) {
                        
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('centres.name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('admin.centres.index', ['title' => $this->title, 'user' => session()->get('user')]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar centros, contacte con el administrador');
        } 

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $islands = DB::table('centres')
                    ->select('island')
                    ->where('island', '!=', null)
                    ->distinct()
                    ->get('island');
        return view('admin.centres.create', [ 'title' => $this->title
                                             ,'islands' => $islands ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'name'       => 'required',
                'label'      => 'required',
                'address'    => 'required',
                'phone'      => 'required',
                'island'     => 'required',
                'email'      => 'required|email',
                'image'      => 'required|mimes:jpg'
            ]);

            $params = $request->all();
            $request->file('image')->storeAs( 'public/img/centres/',  $request->alias_img . ".jpg");
            $pathImg = env('STORAGE_IMGS_CENTRES');
            $params['image'] = $pathImg. $request['alias_img'].'.jpg';
            $centre = Centre::create($params);   

            return redirect()->action('CentreController@index')
    
                            ->with('success','Centro creado correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Ha ocurrido un error en validación de formulario');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al crear centro, contacte con el administrador');
        }
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
            $centre = Centre::find($id);
            $islands = DB::table('centres')
                        ->select('island')
                        ->whereNotNull('island')
                        ->distinct()
                        ->get('island');

        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al cargar centro para editar, contacte con el administrador');
        }     
        return view('admin.centres.edit', [ 'title'   => $this->title
                                          , 'centre'  => $centre
                                          , 'islands' => $islands ]);
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
            $centre = Centre::find($id);
            $params = $request->all();

            if (isset($request[ 'image' ]) ) {
                $request-> validate( 
                    ['label'      => 'required',
                    'address'    => 'required',
                    'phone'      => 'required',
                    'email'      => 'required',
                    'image' => 'mimes:jpg'] );

                    $res = getimagesize($request->file('image'));
                    $w = $res[0]; 
                    $h = $res[1]; 
                    if ($w != 2320 && $h != 1547 ) {
                        return back()->with('error', 'tamaño de imagen incorrecto');
                    }

                    $request->file('image')->storeAs( 'public/img/centres',  $centre->alias_img . ".jpg");
                    $pathImg = env('STORAGE_IMGS_CENTRES');

                    $params['image'] = $pathImg. $centre->alias_img.'.jpg';  
                    } else{
                    $request->validate([
                        'label'      => 'required',
                        'address'    => 'required',
                        'phone'      => 'required',
                        'email'      => 'required'        
                        ]); 
                }

            $centre->update($params);

            return redirect()->action('CentreController@index')
    
                            ->with('success','Centro actualizado correctamente');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Formulario incompleto, faltan campos requeridos');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al actualizar centro, contacte con el administrador');
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
        //
        try{
            $params = $request->all();
            $id=$params['id'];
            $centre = Centre::find($id);
            $fields['cancellation_date'] = date("Y-m-d H:i:s"); 
            $centre->update($fields);

            return response()->json([
                'success' => true,  'mensaje' => 'Centro eliminado correctamente'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->to('home')->with('error', 'Ha ocurrido un error al eliminar centro, contacte con el administrador');
        }
        
    }
}
