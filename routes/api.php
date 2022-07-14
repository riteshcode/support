<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SettingController;
// use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\SubscriberBusinessController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TimezoneController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\TempGalleryController;
use App\Http\Controllers\Api\WebsiteSettingController;
use App\Http\Controllers\Api\WebsiteSettingGroupController;
use App\Http\Controllers\Api\PagesController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\MenuGroupController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BannerGroupController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostDescriptionController;
use App\Http\Controllers\Api\NewsLetterController;
use App\Http\Controllers\Api\EmailGroupController;
use App\Http\Controllers\Api\EmailTemplateController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\AppSettingGroupController;
use App\Http\Controllers\Api\WebTemplateController;
use App\Http\Controllers\Api\WebTemplateSettingController;
use App\Http\Controllers\Api\WebFunctionController;
use App\Http\Controllers\Api\TemplateComponentController;
use App\Http\Controllers\Api\TemplateComponentSettingController;
use App\Http\Controllers\Api\WebTestimonialController;
use App\Http\Controllers\Api\ShipingMethodController;









/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [UserController::class,'login']);

//Language API
Route::post('language/details', [LanguageController::class,'details']);
Route::get('subscriptions/history/{id}', [SubscriptionController::class,'history']);

Route::group(['middleware' => ['ApiTokenCheck']], function() {


    Route::post('temp/upload/file', [TempGalleryController::class,'store']);

    Route::post('users', [UserController::class,'index'])   ;
    Route::post('user/edit', [UserController::class,'edit'])   ;
    Route::post('user/store', [UserController::class,'store']);
    Route::post('user/update', [UserController::class,'update']);
    Route::post('user/delete', [UserController::class,'destroy']);

    //Subscribers API
    Route::group(['prefix' => 'subscribers'], function() {
        Route::post('/all', [SubscriberBusinessController::class,'index_all']);
        Route::post('/', [SubscriberBusinessController::class,'index']);
        Route::post('/add', [SubscriberBusinessController::class,'add']);
        Route::post('/edit', [SubscriberBusinessController::class,'edit']);
        Route::post('/update', [SubscriberBusinessController::class,'update']);
        Route::post('/changeStatus', [SubscriberBusinessController::class,'changeStatus']);
    });
    //End Subscribers API
    
    //Subscription API
    Route::group(['prefix' => 'subscriptions'], function() {
        Route::post('/', [SubscriptionController::class,'index']);
        Route::post('/add', [SubscriptionController::class,'add']);
        Route::post('/edit', [SubscriptionController::class,'edit']);
        Route::post('/update', [SubscriptionController::class,'update']);
        Route::post('/details', [SubscriptionController::class,'details']);
        Route::post('/industry_plan', [SubscriptionController::class,'industry_plan']);
        Route::post('/changeStatus', [SubscriptionController::class,'changeStatus']);

    });
    //End Subscription API

    //Plan API
    Route::group(['prefix' => 'plan'], function() {
        Route::post('/', [PlanController::class,'index']);
        Route::post('/add', [PlanController::class,'add']);
        Route::post('/edit', [PlanController::class,'edit']);
        Route::post('/update', [PlanController::class,'update']);
        Route::post('/details', [PlanController::class,'details']);
        Route::post('/changeStatus', [PlanController::class,'changeStatus']);
    });
    //End Plan API
    
    //Language API
    Route::group(['prefix' => 'language'], function() {
        Route::post('/', [LanguageController::class,'index']);
        Route::post('/edit', [LanguageController::class,'edit']);
        Route::post('/update', [LanguageController::class,'update']);
        Route::post('/store', [LanguageController::class,'store']);
        Route::post('/changeStatus', [LanguageController::class,'changeStatus']);
        Route::post('/all', [LanguageController::class,'index_all']);
    });
    //End Language API

    //Translation API
    Route::group(['prefix' => 'translation'], function() {
        Route::post('/edit', [LanguageController::class,'trans_edit']);
        Route::post('/update', [LanguageController::class,'trans_update']);
        Route::post('/add', [LanguageController::class,'trans_add']);
        Route::post('/add_key', [LanguageController::class,'trans_add_key']);
        Route::post('/trans_edit_key_value', [LanguageController::class,'trans_edit_key_value']);
        Route::post('/trans_delete_key', [LanguageController::class,'trans_delete_key']);
    });
    //End Translation API
    
    //Country API
    Route::group(['prefix' => 'country'], function() {
        Route::post('/all', [CountryController::class,'index_all']);
        Route::post('/', [CountryController::class,'index']);
        Route::post('/store', [CountryController::class,'store']);
        Route::post('/edit', [CountryController::class,'edit']);
        Route::post('/update', [CountryController::class,'update']);
        Route::post('/delete', [CountryController::class,'destroy']);
    });
    //End Country API
    //Currency API
    Route::group(['prefix' => 'currency'], function() {
        Route::post('/all', [CurrencyController::class,'index_all']);
        Route::post('/', [CurrencyController::class,'index']);
        Route::post('/store', [CurrencyController::class,'store']);
        Route::post('/edit', [CurrencyController::class,'edit']);
        Route::post('/update', [CurrencyController::class,'update']);
        Route::post('/changeStatus', [CurrencyController::class,'changeStatus']);
    });
    //End Currency API

    //TimeZone API
    Route::group(['prefix' => 'timezone'], function() {
        Route::post('/all', [TimezoneController::class,'index_all']);
    });
    //End TimeZone API
    
    // Route::post('category', [CategoryController::class,'index']);
    // Route::post('category/store', [CategoryController::class,'store']);

    //  setting prefix FOR ADMIN/SUPERADMIN
    Route::group(['prefix'=>'setting'], function(){
        Route::post('/', [SettingController::class,'index']);
        Route::post('store', [SettingController::class,'store']);
        Route::post('general_first', [SettingController::class,'general_first']);
        Route::post('general', [SettingController::class,'general']);
        Route::post('payment_first', [SettingController::class,'payment_first']);
        Route::post('payment', [SettingController::class,'payment']);
        Route::post('website_first', [SettingController::class,'website_first']);
        Route::post('website', [SettingController::class,'website']);
        Route::post('notification_first', [SettingController::class,'notification_first']);
        Route::post('notification', [SettingController::class,'notification']);
    });
    // end setting prefix

    // notification route
    Route::group(['prefix'=>'notification'], function(){
        Route::post('get_all_notification', [NotificationController::class, 'get_all_notification']);
        Route::post('get_unread_notification', [NotificationController::class, 'get_unread_notification']);
        Route::post('is_read_status', [NotificationController::class, 'is_read_status']);
    });
    // end notification
    
    // module prefix
    Route::group(['prefix'=>'module'], function(){
        Route::post('/all', [ModuleController::class,'index_all']);
        Route::post('/', [ModuleController::class,'index']);
        Route::post('/store', [ModuleController::class,'store']);
        Route::post('/update/sort_order', [ModuleController::class,'update_sort_order']);
        Route::post('/edit', [ModuleController::class,'edit']);
        Route::post('/update', [ModuleController::class,'update']);
        Route::post('/change_status', [ModuleController::class,'changeStatus']);
        Route::post('/section/edit', [ModuleController::class,'section_edit']);
        Route::post('/section/update', [ModuleController::class,'section_update']);
        Route::post('/store_module_section', [ModuleController::class,'store_module_section']);
        Route::post('/section_list', [ModuleController::class,'section_list']);
        Route::post('/quick_section_list', [ModuleController::class,'quick_section_list']);

    });
    // end module prefix

    // industry prefix
    Route::group(['prefix'=>'industry'], function(){
        Route::post('/', [IndustryController::class,'index']);
        Route::post('/all', [IndustryController::class,'index_all']);
        Route::post('/store', [IndustryController::class,'store']);
        Route::post('/edit', [IndustryController::class,'edit']);
        Route::post('/update', [IndustryController::class,'update']);
        Route::post('/create', [IndustryController::class,'create']);
        Route::post('/changeStatus', [IndustryController::class,'changeStatus']);
    });
    // end industry prefix

    // Business prefix
    Route::group(['prefix'=>'business'], function(){
        Route::post('/', [BusinessController::class,'index']);
        Route::post('/store', [BusinessController::class,'store']);
        Route::post('/edit', [BusinessController::class,'edit']);
        Route::post('/update', [BusinessController::class,'update']);
    }); 
    // end Business prefix

    // Payment Methods prefix
    Route::group(['prefix'=>'paymentMethod'], function(){
        Route::post('/', [PaymentMethodController::class,'index']);
        Route::post('/list', [PaymentMethodController::class,'list']);
        Route::post('/store', [PaymentMethodController::class,'store']);
        Route::post('/edit', [PaymentMethodController::class,'edit']);
        Route::post('/update', [PaymentMethodController::class,'update']);
        Route::post('/create', [PaymentMethodController::class,'create']);
        Route::post('/paymentUpdate', [PaymentMethodController::class,'payment_update']);
        Route::post('/changeStatus', [PaymentMethodController::class,'changeStatus']);
    }); 
    // end Payment prefix

       // shipping Methods prefix
       Route::group(['prefix'=>'shippingMethod'], function(){
        Route::post('/', [ShipingMethodController::class,'index']);
        Route::post('/list', [ShipingMethodController::class,'list']);
        Route::post('/store', [ShipingMethodController::class,'store']);
        Route::post('/edit', [ShipingMethodController::class,'edit']);
        Route::post('/update', [ShipingMethodController::class,'update']);
        Route::post('/shippingUpdate', [ShipingMethodController::class,'shipping_update']);
        Route::post('/changeStatus', [ShipingMethodController::class,'changeStatus']);
        Route::post('/create', [ShipingMethodController::class,'create']);

    }); 
    // end  prefix


        // Website Settings Methods prefix
        Route::group(['prefix'=>'websettings'], function(){
            Route::post('/', [WebsiteSettingController::class,'index']);
            Route::post('/store', [WebsiteSettingController::class,'store']);
            Route::post('/edit', [WebsiteSettingController::class,'edit']);
            Route::post('/update', [WebsiteSettingController::class,'update']);
            Route::post('/destroy', [WebsiteSettingController::class,'destroy']);
            Route::post('/create', [WebsiteSettingController::class,'create']);
        }); 


        // Website Banner Methods prefix
        Route::group(['prefix'=>'banner'], function(){
             Route::post('/list', [BannerController::class,'list']);
            Route::post('/store', [BannerController::class,'store']);
            Route::post('/edit', [BannerController::class,'edit']);
            Route::post('/update', [BannerController::class,'update']);
            Route::post('/destroy', [BannerController::class,'destroy']);
            Route::post('/changeStatus', [BannerController::class,'changeStatus']);
        }); 
         
         //Web Banner Group prefix
        Route::group(['prefix'=>'bannerGroup'], function(){
            Route::post('/', [BannerController::class,'index']);
            Route::post('/store', [BannerGroupController::class,'store']);
            Route::post('/edit', [BannerGroupController::class,'edit']);
            Route::post('/update', [BannerGroupController::class,'update']);
            Route::post('/changeStatus', [BannerGroupController::class,'changeStatus']);
            
        }); 

       //end Web Banner Group prefix

        // Website Settings Methods prefix
        Route::group(['prefix'=>'websettingsgroup'], function(){
            Route::post('/', [WebsiteSettingGroupController::class,'index']);
            Route::post('/store', [WebsiteSettingGroupController::class,'store']);
            Route::post('/edit', [WebsiteSettingGroupController::class,'edit']);
            Route::post('/update', [WebsiteSettingGroupController::class,'update']);
            Route::post('/destroy', [WebsiteSettingGroupController::class,'destroy']);
       
        }); 
        // end Website Settings prefix

        //starting of webpages
        Route::group(['prefix'=>'pages'], function(){
            Route::post('/', [PagesController::class,'index']);
            Route::post('/store', [PagesController::class,'store']);
            Route::post('/edit', [PagesController::class,'edit']);
            Route::post('/update', [PagesController::class,'update']);
            Route::post('/destroy', [PagesController::class,'destroy']);
            Route::post('/changeStatus', [PagesController::class,'changeStatus']);
       
        });
        //ending of webpages


          //Web Menu prefix
          Route::group(['prefix'=>'menu'], function(){
            Route::post('/', [MenuController::class,'list']);
            Route::post('/store', [MenuController::class,'store']);
            Route::post('/edit', [MenuController::class,'edit']);
            Route::post('/update', [MenuController::class,'update']);
            Route::post('/destroy', [MenuController::class,'destroy']);
            Route::post('/create', [MenuController::class,'create']);
            Route::post('/changeStatus', [MenuController::class,'changeStatus']);
            
        }); 
       //end Web Menu prefix

        //Web Menu Group prefix
            Route::group(['prefix'=>'menugroup'], function(){
            Route::post('/', [MenuGroupController::class,'index']);
            Route::post('/store', [MenuGroupController::class,'store']);
            Route::post('/edit', [MenuGroupController::class,'edit']);
            Route::post('/update', [MenuGroupController::class,'update']);
            Route::post('/destroy', [MenuGroupController::class,'destroy']);
            Route::post('/changeStatus', [MenuGroupController::class,'changeStatus']);
            
        }); 
       //end Web Menu Group prefix


        //Web emailTemplate prefix
          Route::group(['prefix'=>'emailTemplate'], function(){
            Route::post('/', [EmailTemplateController::class,'list']);
            Route::post('/store', [EmailTemplateController::class,'store']);
            Route::post('/edit', [EmailTemplateController::class,'edit']);
            Route::post('/update', [EmailTemplateController::class,'update']);
            Route::post('/destroy', [EmailTemplateController::class,'destroy']);
            Route::post('/create', [EmailTemplateController::class,'create']);
            Route::post('/changeStatus', [EmailTemplateController::class,'changeStatus']);
            
        }); 
       //end Web emailTemplate prefix

        //Web emailGroup prefix
            Route::group(['prefix'=>'emailGroup'], function(){
            Route::post('/', [EmailGroupController::class,'index']);
            Route::post('/store', [EmailGroupController::class,'store']);
            Route::post('/edit', [EmailGroupController::class,'edit']);
            Route::post('/update', [EmailGroupController::class,'update']);
            Route::post('/destroy', [EmailGroupController::class,'destroy']);
            Route::post('/changeStatus', [EmailGroupController::class,'changeStatus']);
            
        }); 
       //ende mailGroup prefix


            //Web POST Prefix

            Route::group(['prefix'=>'post'], function(){
            Route::post('/', [PostController::class,'index']);
            Route::post('/store', [PostController::class,'store']);
            Route::post('/edit', [PostController::class,'edit']);
            Route::post('/update', [PostController::class,'update']);
            Route::post('/destroy', [PostController::class,'destroy']);
            Route::post('/changeStatus', [PostController::class,'changeStatus']);
            
        }); 

        //end Post  Prefix

        //Web function Prefix
         Route::group(['prefix'=>'webfunction'], function(){
            Route::post('/', [WebFunctionController::class,'index']);
            Route::post('/changeStatus', [WebFunctionController::class,'changeStatus']);
            
        }); 

         //end prefix



       //Web News Letter Prefix

            Route::group(['prefix'=>'newsletter'], function(){
            Route::post('/', [NewsLetterController::class,'index']);
            Route::post('/store', [NewsLetterController::class,'store']);
            Route::post('/edit', [NewsLetterController::class,'edit']);
            Route::post('/update', [NewsLetterController::class,'update']);
            Route::post('/destroy', [NewsLetterController::class,'destroy']);
            Route::post('/changeStatus', [NewsLetterController::class,'changeStatus']);
            Route::post('/ImportFile', [NewsLetterController::class,'import_file']);
        }); 

       
       //end News letter prefix  


        
          // App Settings Methods prefix
        Route::group(['prefix'=>'appsettings'], function(){
            Route::post('/', [AppSettingController::class,'index']);
            Route::post('/store', [AppSettingController::class,'store']);
            Route::post('/edit', [AppSettingController::class,'edit']);
            Route::post('/update', [AppSettingController::class,'update']);
            Route::post('/destroy', [AppSettingController::class,'destroy']);
            Route::post('/create', [AppSettingController::class,'create']);
        }); 

        //end app setting prefix

        // App Settings Group Methods prefix
        Route::group(['prefix'=>'appsettingsgroup'], function(){
            Route::post('/', [AppSettingGroupController::class,'index']);
            Route::post('/store', [AppSettingGroupController::class,'store']);
            Route::post('/edit', [AppSettingGroupController::class,'edit']);
            Route::post('/update', [AppSettingGroupController::class,'update']);
            Route::post('/destroy', [AppSettingGroupController::class,'destroy']);
         
        }); 
        // end app Settings prefix

        
           Route::group(['prefix'=>'templateSetting'], function(){
            Route::post('/', [WebTemplateSettingController::class,'index']);
            Route::post('/store', [WebTemplateSettingController::class,'store']);   
         
        }); 



        // Web Templates prefix
        Route::group(['prefix'=>'template'], function(){
             Route::post('/', [WebTemplateController::class,'index_all']);
             Route::post('/list',[WebTemplateController::class,'create']);
             Route::post('/detail', [WebTemplateController::class,'template_detail']);
             Route::post('/store', [WebTemplateController::class,'template_store']);
             Route::post('/edit', [WebTemplateController::class,'template_edit']);
             Route::post('/update', [WebTemplateController::class,'template_update']);
             Route::post('/componentlist', [WebTemplateController::class,'component_setting_list']);
             Route::post('/componentsetting/store', [WebTemplateController::class,'component_setting_store']);
              Route::post('/componentsetting/delete', [WebTemplateController::class,'destroy']);
         
             Route::post('/sectionlist', [WebTemplateController::class,'section_list']);
             Route::post('/sectionStore', [WebTemplateController::class,'template_section_store']);
             Route::post('/sectionEdit', [WebTemplateController::class,'section_edit']);
             Route::post('/sectionUpdate', [WebTemplateController::class,'template_section_update']);
             Route::post('/sectionGroupStore', [WebTemplateController::class,'template_section_group_store']); 
             Route::post('/sectionGroupedit', [WebTemplateController::class,'section_group_edit']);
             Route::post('/sectionGroupUpdate', [WebTemplateController::class,'template_section_group_update']);
             Route::post('/changeStatus', [WebTemplateController::class,'changeStatus']);      
           
        }); 
        // end Web Templates prefix

        //Template Component 
          
        Route::group(['prefix' => 'templateComponent'], function() {
         Route::post('/', [TemplateComponentController::class,'index']);
         Route::post('/create', [TemplateComponentController::class,'create']);
         Route::post('/store', [TemplateComponentController::class,'store']);
         Route::post('/edit', [TemplateComponentController::class,'edit']);
         Route::post('/update', [TemplateComponentController::class,'update']);
         Route::post('/changeStatus', [TemplateComponentController::class,'changeStatus']);
    });
    
    //END  

     //Template Component Setting

     Route::group(['prefix' => 'componentsetting'], function() {
         Route::post('/', [TemplateComponentSettingController::class,'index']);
         Route::post('/store', [TemplateComponentSettingController::class,'store']);
         Route::post('/edit', [TemplateComponentSettingController::class,'edit']);
         Route::post('/update', [TemplateComponentSettingController::class,'update']);
         Route::post('/changeStatus', [TemplateComponentSettingController::class,'changeStatus']);
         Route::post('/sortOrder', [TemplateComponentSettingController::class,'sortOrder']);
    });
    
    //Web testimonial Prefix
         Route::group(['prefix'=>'testimonial'], function(){
            Route::post('/', [WebTestimonialController::class,'index']);
            Route::post('/changeStatus', [WebTestimonialController::class,'changeStatus']);
            
        }); 

         //end prefix
        

           
    
});

