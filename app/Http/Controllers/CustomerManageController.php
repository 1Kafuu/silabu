<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CustomerManageController extends Controller
{
    public function indexBlob()
    {
        $customers = Customer::where('foto_path', null)->get();
        return view('admin.customer.customer-blob', compact('customers'));
    }

    public function createBlob()
    {
        return view('admin.customer.create-customer-blob');
    }

    public function storeBlob(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kodepos' => 'required|integer',
            'foto_blob' => 'nullable',
        ]);

        $customer = Customer::create($validated);

        if ($customer) {
            session()->flash('success', 'Customer created successfully!');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('manage-customerBlob')
            ]);
        } else {
            session()->flash('error', 'Failed to create customer. Please try again.');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function editBlob($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customer.update-customer-blob', compact('customer'));
    }

    public function updateBlob(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kodepos' => 'required|integer',
            'foto_blob' => 'nullable',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->update($validated);

        if ($customer) {
            session()->flash('success', 'Customer updated successfully!');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('manage-customerBlob')
            ]);
        } else {
            session()->flash('error', 'Failed to update customer. Please try again.');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function deleteBlob($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('manage-customerBlob')->with('success', 'Customer deleted successfully!');
    }


    public function indexPath()
    {
        $customers = Customer::where('foto_blob', null)->get();
        return view('admin.customer.customer-path', compact('customers'));
    }

    public function createPath()
    {
        return view('admin.customer.create-customer-path');
    }

    public function storePath(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kodepos' => 'required|integer',
            'foto_path' => 'nullable',
        ]);

        $imageData = $request->input('foto_path');

        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'cust_' . time() . '.png';

        Storage::disk('public')->put('customers/' . $imageName, base64_decode($image));

        $customer = Customer::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'provinsi' => $validated['provinsi'],
            'kota' => $validated['kota'],
            'kecamatan' => $validated['kecamatan'],
            'kelurahan' => $validated['kelurahan'],
            'kodepos' => $validated['kodepos'],
            'foto_path' => 'customers/' . $imageName,
        ]);

        if ($customer) {
            session()->flash('success', 'Customer created successfully!');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('manage-customerPath')
            ]);
        } else {
            session()->flash('error', 'Failed to create customer. Please try again.');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function editPath($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customer.update-customer-path', compact('customer'));
    }

    public function updatePath(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kelurahan' => 'required|string|max:255',
            'kodepos' => 'required|integer',
            'foto_path' => 'nullable',
        ]);

        $customer = Customer::findOrFail($id);

        if ($request->filled('foto_path')) {
            $imageData = $request->input('foto_path');

            if ($customer->foto_path) {
                Storage::disk('public')->delete($customer->foto_path);
            }
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $imageName = 'cust_' . time() . '.png';
            Storage::disk('public')->put('customers/' . $imageName, base64_decode($image));
            $customer->foto_path = 'customers/' . $imageName;
        }

        $customer->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'provinsi' => $validated['provinsi'],
            'kota' => $validated['kota'],
            'kecamatan' => $validated['kecamatan'],
            'kelurahan' => $validated['kelurahan'],
            'kodepos' => $validated['kodepos'],
            'foto_path' => $customer->foto_path,
        ]);

        if ($customer) {
            session()->flash('success', 'Customer updated successfully!');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('manage-customerPath')
            ]);
        } else {
            session()->flash('error', 'Failed to update customer. Please try again.');

            $notificationHTML = view('components.notification')->render();

            Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function deletePath($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('manage-customerPath')->with('success', 'Customer deleted successfully!');
    }
}