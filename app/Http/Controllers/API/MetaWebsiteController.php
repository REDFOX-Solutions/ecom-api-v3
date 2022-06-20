<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RestResource;
use App\Model\AvailableLanguages;
use App\Model\Company;
use App\Model\Post;
use App\Model\Products;
use App\Model\Project;
use App\Model\SitePages;
use App\Services\DataConvertionClass;

class MetaWebsiteController extends Controller
{
    /**
     * Method to generate website meta info for page
     * @param $required:
     * - page_name      Page name
     * - active_page     current active page url without lang code e.g. /home
     * - active_lang    page active language
     * @return list of meta info (only one record)
     */
    public function getPageMeta(Request $req){

        
        $lstFilters = $req->all();
        $pageName = $lstFilters["page_name"];
        $activeLang = $lstFilters["active_lang"];
        $activePageRoute = $lstFilters["active_page"];  

        $lstAvaiLangs = AvailableLanguages::get()->toArray();

        $metaInfo = [
            "title" => "",
            "description" => "",
            "image" => "",
            "activeLang" => "en",
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "activePage" => ""
        ];

        $lstSitePages = SitePages::where("name", $pageName)->get()->toArray();
        if(empty($lstSitePages)){
            return new RestResource(collect([$metaInfo])); 
        }

        $lstCompanies = Company::whereNull("main_branch_id")

                                ->get()
                                ->toArray();
        $companyInfo = $lstCompanies[0];
        DataConvertionClass::findLangs2ChangeIndex($companyInfo);
        $companyInfoLang = $companyInfo["langs"][$activeLang];

        $activePage = $lstSitePages[0];
        DataConvertionClass::findLangs2ChangeIndex($activePage);
        $activePageTranslate = isset($activePage["langs"]) && isset($activePage["langs"][$activeLang]) ? $activePage["langs"][$activeLang] : [];

        $imageUrl = isset($activePage["image"]) ? $activePage["image"] : "";

        if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false){
            $fullImageUrl = $companyInfo["website"] . "/" . $imageUrl;
        }
        
        $metaInfo = [
            "title" => isset($activePageTranslate["title"]) ? $activePageTranslate["title"] . " | " . $companyInfoLang["name"]: "",
            "description" => isset($activePageTranslate["short_desc"]) ? $activePageTranslate["short_desc"] : "",
            "image" => $fullImageUrl,
            "activeLang" => $activeLang,
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "activePage" => $activePageRoute
        ];

        return new RestResource(collect([$metaInfo]));
    }

    /**
     * Method to generate website meta info for page post detail
     * @param $required:
     * - slug           post slug value
     * - active_page     current active page url without lang code e.g. /activity/001
     * - active_lang    current post active language
     * @return list of post meta info (only one record)
     */
    public function getArticleMeta(Request $req){
        $lstFilters = $req->all();
        $slug = $lstFilters["slug"];
        $activeLang = $lstFilters["active_lang"];
        $activePageRoute = $lstFilters["active_page"];
        $lstAvaiLangs = AvailableLanguages::get()
                                            ->toArray();

        $lstPosts = Post::where("slug", $slug)->with(["author"])->get()->toArray();

        $metaInfo = [
            "title" => "",
            "description" => "",
            "image" => "",
            "activeLang" => "",
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "activePage" => ""
        ];
        if(empty($lstPosts)){
            return new RestResource(collect([$metaInfo])); 
        }

        $lstCompanies = Company::whereNull("main_branch_id")
                                ->get()
                                ->toArray();
        $companyInfo = $lstCompanies[0];
        DataConvertionClass::findLangs2ChangeIndex($companyInfo);
        $companyInfoLang = $companyInfo["langs"][$activeLang];

        $activePost = $lstPosts[0];
        DataConvertionClass::findLangs2ChangeIndex($activePost);
        $activePostTranslate = isset($activePost["langs"]) && isset($activePost["langs"][$activeLang]) ? $activePost["langs"][$activeLang] : [];

        $imageUrl = isset($activePost["main_photo"]) ? $activePost["main_photo"] : "";

        if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false){
            $fullImageUrl = $companyInfo["website"] . "/" . $imageUrl;
        }
        

        $author = [];
        $authorTranslate = [];
        if(isset($activePost["author"])){
            $author = $activePost["author"];  
            DataConvertionClass::findLangs2ChangeIndex($author);
            $authorTranslate = $author["langs"][$activeLang];
        } 
        
        $metaInfo = [
            "title" => isset($activePostTranslate["title"]) ? $activePostTranslate["title"] . " | " . $companyInfoLang["name"] : "",
            "description" => isset($activePostTranslate["short_desc"]) ? $activePostTranslate["short_desc"] : "",
            "image" => $fullImageUrl,
            "activeLang" => $activeLang,
            "isArticle" => 1,
            "available_langs" => $lstAvaiLangs,
            "author" => isset($authorTranslate["fullname"]) ? $authorTranslate["fullname"] : '',
            "posted_at" => $activePost["posted_at"],
            "activePage" => $activePageRoute
        ];

        return new RestResource(collect([$metaInfo]));
    }

    /**
     * Method to generate website meta info for page product detail
     * @param $required:
     * - slug           post slug value
     * - active_page     current active page url without lang code e.g. /activity/001
     * - active_lang    current post active language
     * @return list of post meta info (only one record)
     */
    public function getProductMeta(Request $req){
        $lstFilters = $req->all();
        $slug = $lstFilters["slug"];
        $activeLang = $lstFilters["active_lang"];
        $activePageRoute = $lstFilters["active_page"];
        $lstAvaiLangs = AvailableLanguages::get()
                                            ->toArray();

        $lstProducts = Products::where("slug", $slug)->get()->toArray();

        $metaInfo = [
            "title" => "",
            "description" => "",
            "image" => "",
            "activeLang" => "",
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "activePage" => ""
        ];
        if(empty($lstProducts)){
            return new RestResource(collect([$metaInfo])); 
        }

        $lstCompanies = Company::whereNull("main_branch_id")
                                ->get()
                                ->toArray();
        $companyInfo = $lstCompanies[0];
        DataConvertionClass::findLangs2ChangeIndex($companyInfo);
        $companyInfoLang = $companyInfo["langs"][$activeLang];

        $activeProduct = $lstProducts[0];
        DataConvertionClass::findLangs2ChangeIndex($activeProduct);
        $activeProductTranslate = isset($activeProduct["langs"]) && isset($activeProduct["langs"][$activeLang]) ? $activeProduct["langs"][$activeLang] : [];
 
        $imageUrl = isset($activeProduct["photo"]) ? $activeProduct["photo"] : "";

        if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false){
            $fullImageUrl = $companyInfo["website"] . "/" . $imageUrl;
        }
        
        $metaInfo = [
            "title" => isset($activeProductTranslate["name"]) ? $activeProductTranslate["name"] . " | " . $companyInfoLang["name"] : "",
            "description" => isset($activeProductTranslate["short_desc"]) ? $activeProductTranslate["short_desc"] : "",
            "image" => $fullImageUrl,
            "activeLang" => $activeLang,
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "author" => '', 
            "activePage" => $activePageRoute
        ];

        return new RestResource(collect([$metaInfo]));
    }

    /**
     * Method to generate website meta info for page product detail
     * @param $required:
     * - slug           post slug value
     * - active_page     current active page url without lang code e.g. /activity/001
     * - active_lang    current post active language
     * @return list of project meta info (only one record)
     */
    public function getProjectMeta(Request $req){
        $lstFilters = $req->all();
        $slug = $lstFilters["slug"];
        $activeLang = $lstFilters["active_lang"];
        $activePageRoute = $lstFilters["active_page"];
        $lstAvaiLangs = AvailableLanguages::get()
                                            ->toArray();

        $lstProjects = Project::where("slug", $slug)->get()->toArray();

        $metaInfo = [
            "title" => "",
            "description" => "",
            "image" => "",
            "activeLang" => "",
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "activePage" => ""
        ];
        if(empty($lstProjects)){
            return new RestResource(collect([$metaInfo])); 
        }

        $lstCompanies = Company::whereNull("main_branch_id")
                                ->get()
                                ->toArray();
        $companyInfo = $lstCompanies[0];
        DataConvertionClass::findLangs2ChangeIndex($companyInfo);
        $companyInfoLang = $companyInfo["langs"][$activeLang];

        $activeProject = $lstProjects[0];
        DataConvertionClass::findLangs2ChangeIndex($activeProject);
        $activeProjectTranslate = isset($activeProject["langs"]) && isset($activeProject["langs"][$activeLang]) ? $activeProject["langs"][$activeLang] : [];
 
        $imageUrl = isset($activeProject["thumbnail"]) ? $activeProject["thumbnail"] : "";

        if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false){
            $fullImageUrl = $companyInfo["website"] . "/" . $imageUrl;
        }
        
        $metaInfo = [
            "title" => isset($activeProjectTranslate["title"]) ? $activeProjectTranslate["title"] . " | " . $companyInfoLang["name"] : "",
            "description" => isset($activeProjectTranslate["description"]) ? $activeProjectTranslate["description"] : "",
            "image" => $fullImageUrl,
            "activeLang" => $activeLang,
            "isArticle" => 0,
            "available_langs" => $lstAvaiLangs,
            "author" => '', 
            "activePage" => $activePageRoute
        ];

        return new RestResource(collect([$metaInfo]));
    }
}
