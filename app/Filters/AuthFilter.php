<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            $uri = $request->getUri();
            $path = '/' . ltrim($uri->getPath(), '/');
            $query = $uri->getQuery();
            $next = $query !== '' ? $path . '?' . $query : $path;

            return redirect()->to(base_url('login?next=' . rawurlencode($next)));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
?>
