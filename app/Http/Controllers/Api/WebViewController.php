<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebViewController extends Controller
{
    public function home()
    {
        return view('web.home');
    }

    public function products()
    {
        return view('web.products');
    }

    public function productDetail($id)
    {
        return view('web.product-detail', ['id' => $id]);
    }

    public function productCategory($id)
    {
        return view('web.product-category', ['id' => $id]);
    }

    public function warrantyCheck()
    {
        return view('web.warranty-check');
    }

    public function about()
    {
        return view('web.about');
    }

    public function contact()
    {
        return view('web.contact');
    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function cart()
    {
        return view('web.cart');
    }

    public function orders()
    {
        return view('web.orders');
    }

    public function orderDetail($id)
    {
        return view('web.order-detail', ['id' => $id]);
    }

    public function profile()
    {
        return view('web.profile');
    }

    public function consignments()
    {
        return view('web.consignments');
    }

    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function csDashboard()
    {
        return view('cs.dashboard');
    }

    public function warehouseDashboard()
    {
        return view('warehouse.dashboard');
    }
}
