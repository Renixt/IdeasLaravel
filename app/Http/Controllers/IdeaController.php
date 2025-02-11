<?php
namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class IdeaController extends Controller
{
    //reglas de validacion
    private array $rules  = [
        'title' => 'required|string|max:100',
        'description' => 'required|string|max:300'
    ];

    private array $errorMessages = [
        'required' => 'El campo :attribute es obligatorio',
        'string' =>'Este campo debe ser de tipo string',
        'max'=> 'El campo :attribute no debe ser mayor a 100 caracteres',
    ];

    public function index(Request $request): View
    {
        //colleciion de objetos de eloquent
        //$ideas = Idea::get();//DB::table('ideas')->get(); // select * from ideas
        
        //filtro se manda a la funcion Ideda.php-scopeMyideas
        $ideas = Idea::myIdeas($request->filtro)->theBest($request->filtro)->get(); //segun el scope de idea.php
        return view('ideas.index',['ideas' => $ideas]);
    }

    public function create() : View 
    {
        return view('ideas.create_or_edit');
    }


    public function store(Request $request): RedirectResponse
    {
       // dd($request->all());
       $validated = $request -> validate($this->rules, $this->errorMessages);



       //usar los modelos para almacenar en la bd
       Idea::create([
        //$request->user()->id
        'user_id' => auth()->user()->id,
        'title' => $validated['title'],
        'description' => $validated['description'],
       ]);

       session()->flash('message', 'Idea creada correctamente!');



       //redirecciono a el nombre d eruta idea.indez, que esta en web.php
       return redirect()->route('idea.index');

    }

    public function edit(Idea $idea): View
    {
        //with enviamos la variable idea a la vista
        return view('ideas.create_or_edit')->with('idea', $idea);

    }

    //req recibe los datos que quiero actualizar dentro del form, idea saber q idea ediateremos
    public function update(Request $request, Idea $idea): RedirectResponse
    {
       /* $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:300',
        ]);*/
        $validated = $request -> validate($this->rules, $this->errorMessages);


        //metodo update :0
        $idea->update($validated);
        session()->flash('message', 'Idea actualizada correctamente!');

        return redirect(route('idea.index'));
    }

    public function show(Idea $idea): View
    {
        return view('ideas.show')->with('idea', $idea);
    }


    public function delete(Idea $idea):RedirectResponse
    {
        $idea-> delete();
        session()->flash('message', 'Idea eliminada correctamente!');

        return redirect(route('idea.index'));
    }

    public function synchronizeLikes(Request $request,Idea $idea) : RedirectResponse
    {
            //recibo que idea actualizare el campo likes
            //guardar el registro de la sociacion en la tabla pivote}
            //si existe la sociacion en la tpivote la elimino y si no existe la pongo(like// quitar like
            //contar los likes de la idea para actualizar la tabla ideas/likes
            
            //recibo req, agarro al usuario, utilizo el metodo muchos a muchos, envio el id de la idea a la que le dieron like con toggle
            //toggle recibe el id va a la tabla pivote y verifica si para el susuario la idea existe (ids asociados),
            //  si no lo agrega, de lo contrario lo borra
            $request->user()->ideasLiked()->toggle([$idea->id]);
            //$idea->users()->toggle([ $request->user()->id])

            //llamo al objeto ideas, a su metedo users y al metodo count, osea cuanros usuarios esta asociados a esa idea
            $idea->update(['likes'=>$idea->users()->count()]);

            return redirect()->route('idea.show', $idea);

    }

}