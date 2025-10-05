<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyTypeRequest;
use App\Models\PropertyType;
use Exception;
use Illuminate\Http\Request;
use DataTables;

class PropertyTypeController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            if($request->ajax()){

                $products = PropertyType::select('*')->latest();

                return Datatables::of($products)
                    ->addIndexColumn()

                    ->addColumn('name', function($row){
                        return $row->name;
                    })

                    ->addColumn('image_url', function($row){
                        $url = asset($row->image_url);
                        return '<img src="' . $url . '" alt="PropertyType Image" style="height:60px;">';
                    })

                    ->addColumn('action', function($row){

                        $btn = "";
                        $btn .= '&nbsp;';

                        $btn .= ' <a href="'.route('propertyTypes.show',$row->id).'" class="btn btn-primary btn-sm action-button edit-product" data-id="'.$row->id.'"><i class="fa fa-edit"></i></a>';

                        $btn .= '&nbsp;';


                        $btn .= ' <a href="#" class="btn btn-danger btn-sm delete-event action-button" data-id="'.$row->id.'"><i class="fa fa-trash"></i></a>';

                        return $btn;
                    })
                    ->rawColumns(['name','image_url','action'])
                    ->make(true);
            }

            return view('admin.propertyTypes.index');
        }catch(Exception $e){
            return response()->json(['status'=>false, 'code'=>$e->getCode(), 'message'=>$e->getMessage()],500);
        }
    }
    public function create()
    {
        return view('admin.propertyTypes.create');
    }
    public function store(PropertyTypeRequest $request)
    {
        DB::beginTransaction();
        try
        {
            if($request->hasFile('file')) {
                $filePath = $this->storeFile($request->file('file'));
                $path = $filePath ?? '';
            }

            $propertyType = new PropertyType();
            $propertyType->title = $request->title;
            $propertyType->img = $path;
            $propertyType->save();

            $notification=array(
                'message' => 'Successfully a event has been added',
                'alert-type' => 'success',
            );
            DB::commit();

            return redirect()->route('propertyTypes.index')->with($notification);

        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in storing event: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function show(PropertyType $propertyType)
    {
        return view('admin.propertyTypes.edit', compact('event'));
    }
    public function edit(PropertyType $propertyType)
    {
        //
    }
    public function update(EventRequest $request, PropertyType $propertyType)
    {
        try
        {
            // Handle file upload
            $path = $propertyType->img;
            if ($request->hasFile('file')) {
                $filePath = $this->updateFile($request->file('file'), $propertyType);
                $path = $filePath ?? '';
            }

            $propertyType->title = $request->title;
            $propertyType->img = $path;
            $propertyType->save();

            $notification=array(
                'message'=>'Successfully the event has been updated',
                'alert-type'=>'success',
            );

            return redirect()->route('propertyTypes.index')->with($notification);

        } catch(Exception $e) {
            // Log the error
            Log::error('Error in updating event: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
    public function destroy(PropertyType $propertyType)
    {
        try
        {
            // Delete the old file if it exists
            $this->deleteOldFile($propertyType);
            $propertyType->delete();
            return response()->json(['status'=>true, 'message'=>'Successfully the event has been deleted']);
        } catch(Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Error in deleting event: ', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $notification=array(
                'message' => 'Something went wrong!!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
    }
}
