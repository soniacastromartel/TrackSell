<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\API\BaseController as BaseController;

class FAQController extends BaseController{

    public function getDataFAQ(){
        $promos = DB::table('faq')->get();
        $arrayFAQ = $promos->toArray();

        $faqNormalized = []; 
        foreach ($arrayFAQ as $index => $faq) {
            $sections = explode('.', $faq->code);
            $images = \Illuminate\Support\Facades\Storage::disk('public')->files('faq/'. $sections[0].'/'.$sections[1] );
            $arrayFAQ['images'] = []; 
            $faqNormalized[$index] = $faq;
            $faqNormalized[$index]->images = [];  
            foreach ($images as $img) {
                $urlFile =env('BASE_API_URL') . \Illuminate\Support\Facades\Storage::url($img);
                $faqNormalized[$index]->images[] = $urlFile; 
            }
        }
        return $this->sendResponse($faqNormalized, 'Ok');
    }
}