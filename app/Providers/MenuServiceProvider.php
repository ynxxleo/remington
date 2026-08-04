<?php

namespace App\Providers;

use App\Models\Extension;
use App\Models\Platform;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // get all data from menu.json file
        $bot = Extension::where('id',1)->first();
        $ico = Extension::where('id',2)->first();
        $mlm = Extension::where('id',3)->first();
        $forex = Extension::where('id',4)->first();
        $plat = Platform::where('id',1)->first();
        if($plat->binary == 1){
            if($bot->status == 1){
                if($ico->status == 1){
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM1234.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM1234.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM123.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM123.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM124.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM124.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM12.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM12.json'));
                        }
                    }
                } else {
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM134.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM134.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM13.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM13.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM14.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM14.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM1.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM1.json'));
                        }
                    }
                }
            } else {
                if($ico->status == 1){
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM234.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM234.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM23.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM23.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM24.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM24.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM2.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM2.json'));
                        }
                        $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM2.json'));
                        $userMenuJson = file_get_contents(resource_path('data/menu-data/uM2.json'));
                    }
                } else {
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM34.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM34.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM3.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM3.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM4.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM4.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM.json'));
                        }
                    }
                }
            }
        } else {
            if($bot->status == 1){
                if($ico->status == 1){
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM1234-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM1234-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM123-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM123-no-binary.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM124-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM124-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM12-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM12-no-binary.json'));
                        }
                    }
                } else {
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM134-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM134-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM13-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM13-no-binary.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM14-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM14-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM1-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM1-no-binary.json'));
                        }
                    }
                }
            } else {
                if($ico->status == 1){
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM234-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM234-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM23-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM23-no-binary.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM24-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM24-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM2-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM2-no-binary.json'));
                        }
                    }
                } else {
                    if($mlm->status == 1){
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM34-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM34-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM3-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM3-no-binary.json'));
                        }
                    } else {
                        if($forex->status == 1){
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM4-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM4-no-binary.json'));
                        } else {
                            $adminMenuJson = file_get_contents(resource_path('data/menu-data/aM-no-binary.json'));
                            $userMenuJson = file_get_contents(resource_path('data/menu-data/uM-no-binary.json'));
                        }
                    }
                }
            }
        }
        $adminMenuData = json_decode($adminMenuJson);
        $userMenuData = json_decode($userMenuJson);
        // Share all menuData to all the views
        \View::share('menuData', [$adminMenuData]);
		\View::share('usermenuData', [$userMenuData]);
    }
}
