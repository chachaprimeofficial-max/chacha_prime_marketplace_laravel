<?php
namespace App\Http\Controllers;
use App\Models\Country;
use Illuminate\Http\Request;
class CountryMarketplaceController extends Controller {
 public function index(Request $request){
  $countries=Country::where('active',1)->orderBy('sort_order')->orderBy('name')->get();
  $selected=$request->session()->get('marketplace_country_id');
  return view('marketplace.country-selector',compact('countries','selected'));
 }
 public function select(Request $request){
  $data=$request->validate(['country_id'=>'required|integer|exists:countries,id']);
  $country=Country::where('id',$data['country_id'])->where('active',1)->firstOrFail();
  $request->session()->put('marketplace_country_id',$country->id);
  $request->session()->put('marketplace_country_code',$country->code);
  if($request->user()) $request->user()->forceFill(['country_id'=>$country->id])->save();
  return back()->with('success','Marketplace country changed to '.$country->name.'.');
 }
}
