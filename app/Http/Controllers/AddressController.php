<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AddressController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        
        return view('profile.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('profile.addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'is_default' => 'boolean',
            'type' => 'required|in:shipping,billing'
        ]);

        $address = Auth::user()->addresses()->create($request->all());

        return redirect()->route('addresses.index')
            ->with('success', 'Địa chỉ đã được thêm thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address): View
    {
        $this->authorize('view', $address);
        
        return view('profile.addresses.show', compact('address'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address): View
    {
        $this->authorize('update', $address);
        
        return view('profile.addresses.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'ward' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'is_default' => 'boolean',
            'type' => 'required|in:shipping,billing'
        ]);

        $address->update($request->all());

        return redirect()->route('addresses.index')
            ->with('success', 'Địa chỉ đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Address $address): RedirectResponse
    {
        $this->authorize('delete', $address);
        
        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Địa chỉ đã được xóa thành công!');
    }

    /**
     * Set address as default
     */
    public function setDefault(Address $address): RedirectResponse
    {
        $this->authorize('update', $address);
        
        // Set all other addresses to non-default
        Auth::user()->addresses()->update(['is_default' => false]);
        
        // Set this address as default
        $address->update(['is_default' => true]);

        return redirect()->route('addresses.index')
            ->with('success', 'Địa chỉ mặc định đã được cập nhật!');
    }
}
