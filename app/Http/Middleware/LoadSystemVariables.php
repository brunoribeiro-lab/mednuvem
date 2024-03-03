<?php

namespace App\Http\Middleware;

use App\Models\VariavelDoSistema;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use View;

class LoadSystemVariables
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          // Supondo que você tenha um método estático que pega todas as variáveis
          $variables = VariavelDoSistema::get()->first();

          // Compartilhe as variáveis com todas as views
          View::share('systemVariables', $variables);
  
          return $next($request);
    }
}
