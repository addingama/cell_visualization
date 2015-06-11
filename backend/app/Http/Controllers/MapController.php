<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use DB;

class MapController extends Controller
{
    public function index(Request $request) {
        $query = 'select * from data';
        if ($request->has('type')) {
            if ($request->input('type') == 'sql') {
                $query = $request->input('data');
            } else {
                $query = 'select * from data ';

                if ($request->has('column') && $request->has('data') && $request->has('operator')) {
                    $query .= ' where ' . $request->input('column') . ' ' . $request->input('operator') . ' "' . $request->input('data') . '"  ';
                }
                $query .= ' order by JAM ASC ';
                if ($request->has('limit')) {
                    $query .= ' limit ' . $request->input('limit');
                }
            }
        }
        $result = DB::select($query);
        // return (new Response($result, 200))->header("Access-Control-Allow-Origin", "*");
        return response()->json($result);
    }
}
