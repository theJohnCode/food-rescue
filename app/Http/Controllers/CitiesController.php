<?php

namespace App\Http\Controllers;

use App\City;

use Illuminate\Http\Request;

class CitiesController extends Controller
{

    protected $imagePath = 'uploads/settings/';

    private function validateAccess()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(404);
        }
    }

    private function getFields()
    {
        return [
            ['ftype' => 'image', 'name' => __("City image ( 200x200 )"), 'id' => "image_up"],
            ['ftype' => 'input', 'name' => "Name", 'id' => "name", 'placeholder' => "Enter street name", 'required' => true],
            ['ftype' => 'input', 'name' => "City 2 - 3 letter short code", 'id' => "alias", 'placeholder' => "Enter street short code ex. ny", 'required' => true],
            ['ftype' => 'input', 'name' => "Header title", 'id' => "header_title", 'placeholder' => "Header title", 'required' => true],
            ['ftype' => 'input', 'name' => "Header subtitle", 'id' => "header_subtitle", 'placeholder' => "Header subtitle", 'required' => true],

        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->validateAccess();
        return view('cities.index', ['setup' => [
            'title' => "Cities",
            'action_link' => route('cities.create'),
            'action_name' => "Add new street",
            'items' => City::paginate(10),
            'item_names' => "cities"
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->validateAccess();
        return view('general.form', [
            'setup' => [
                'title' => "New street",
                'action_link' => route('cities.index'),
                'action_name' => __("Back"),
                'iscontent' => true,
                'action' => route('cities.store'),
                'breadcrumbs' => [
                    [__('Cities'), route('cities.index')],
                    [__('New'), null]
                ],
            ],
            'fields' => $this->getFields()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateAccess();
        //Validate first
        $request->validate([
            'name' => ['required', 'string', 'unique:cities,name', 'max:255'],
            'alias' => ['required', 'string', 'unique:cities,alias', 'string', 'max:255']
        ]);

        $city = City::create([
            'name' => $request->name,
            'alias' => $request->alias,
            'image' => "",
            'header_title' => $request->header_title,
            'header_subtitle' => $request->header_subtitle,

        ]);

        $city->save();

        if ($request->hasFile('image_up')) {
            $city->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image_up,
                [
                    ['name' => 'large', 'w' => 590, 'h' => 590],
                    ['name' => 'medium', 'w' => 300, 'h' => 300],
                    ['name' => 'thumbnail', 'w' => 200, 'h' => 200]
                ]
            );

            $city->update();
        }

        return redirect()->route('cities.index')->withStatus(__('Street was added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(City $street)
    {
        $this->validateAccess();
        $fields = $this->getFields();
        $fields[0]['value'] = $street->logo;
        $fields[1]['value'] = $street->name;
        $fields[2]['value'] = $street->alias;
        $fields[3]['value'] = $street->header_title;
        $fields[4]['value'] = $street->header_subtitle;

        //dd($option);
        return view('general.form', [
            'setup' => [
                'title' => __("Edit street") . " " . $street->name,
                'action_link' => route('cities.index'),
                'action_name' => __("Back"),
                'iscontent' => true,
                'isupdate' => true,
                'action' => route('cities.update', ['street' => $street->id]),
                'breadcrumbs' => [
                    [__('Streets'), route('cities.index')],
                    [$street->name, null]
                ],
            ],
            'fields' => $fields
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $street)
    {
        $this->validateAccess();

        $street->name = $request->name;
        $street->alias = $request->alias;
        $street->header_title = $request->header_title;
        $street->header_subtitle = $request->header_subtitle;

        if ($request->hasFile('image_up')) {
            $street->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image_up,
                [
                    ['name' => 'large', 'w' => 590, 'h' => 590],
                    ['name' => 'medium', 'w' => 300, 'h' => 300],
                    ['name' => 'thumbnail', 'w' => 200, 'h' => 200]
                ]
            );
        }

        $street->update();

        return redirect()->route('cities.index')->withStatus(__('Street was updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        $this->validateAccess();
        $city->delete();
        return redirect()->route('cities.index')->withStatus(__('Item was deleted.'));
    }
}
