<?php

namespace Hanafalah\ModuleWorkspace\Facades;

use Hanafalah\ModuleWorkspace\ModuleWorkspace;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void exceptionRespond(Exceptions $exceptions)
 */
class Workspace extends Facade
{

   protected static function getFacadeAccessor()
   {
      return ModuleWorkspace::class;
   }
}
