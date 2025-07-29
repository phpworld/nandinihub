<?php

namespace App\Controllers;

use App\Models\UserAddressModel;

class UserAddressController extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }
        $addressModel = new UserAddressModel();
        $addresses = $addressModel->getUserAddresses($userId);
        return view('user/addresses/index', ['addresses' => $addresses]);
    }

    public function add()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $addressModel = new UserAddressModel();

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'user_id' => $userId,
                'name' => $this->request->getPost('name'),
                'phone' => $this->request->getPost('phone'),
                'address_line1' => $this->request->getPost('address_line1'),
                'address_line2' => $this->request->getPost('address_line2'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'pincode' => $this->request->getPost('pincode'),
                'country' => $this->request->getPost('country') ?: 'India',
                'landmark' => $this->request->getPost('landmark'),
                'is_default' => $this->request->getPost('is_default') ? 1 : 0
            ];

            if ($addressModel->save($data)) {
                if ($data['is_default']) {
                    $addressModel->setAsDefault($addressModel->getInsertID(), $userId);
                }
                session()->setFlashdata('success', 'Address added successfully.');
                return redirect()->to('/addresses');
            } else {
                // Get validation errors
                $errors = $addressModel->errors();
                $errorMessage = 'Failed to add address. ';
                if (!empty($errors)) {
                    $errorMessage .= 'Errors: ' . implode(', ', $errors);
                }
                session()->setFlashdata('error', $errorMessage);
                session()->setFlashdata('validation_errors', $errors);
                return redirect()->back()->withInput();
            }
        }

        // Get existing addresses for display
        $addresses = $addressModel->getUserAddresses($userId);
        return view('user/addresses/add', ['addresses' => $addresses]);
    }

    public function edit($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $addressModel = new UserAddressModel();
        $address = $addressModel->where(['id' => $id, 'user_id' => $userId])->first();
        if (!$address) {
            session()->setFlashdata('error', 'Address not found.');
            return redirect()->to('/addresses');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'phone' => $this->request->getPost('phone'),
                'address_line1' => $this->request->getPost('address_line1'),
                'address_line2' => $this->request->getPost('address_line2'),
                'city' => $this->request->getPost('city'),
                'state' => $this->request->getPost('state'),
                'pincode' => $this->request->getPost('pincode'),
                'country' => $this->request->getPost('country') ?: 'India',
                'landmark' => $this->request->getPost('landmark'),
                'is_default' => $this->request->getPost('is_default') ? 1 : 0
            ];

            if ($addressModel->update($id, $data)) {
                if ($data['is_default']) {
                    $addressModel->setAsDefault($id, $userId);
                }
                session()->setFlashdata('success', 'Address updated successfully.');
                return redirect()->to('/addresses');
            } else {
                session()->setFlashdata('error', 'Failed to update address. Please check your information.');
                return redirect()->back()->withInput();
            }
        }

        return view('user/addresses/edit', ['address' => $address]);
    }

    public function delete($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $addressModel = new UserAddressModel();
        $address = $addressModel->where(['id' => $id, 'user_id' => $userId])->first();
        if ($address) {
            if ($addressModel->delete($id)) {
                session()->setFlashdata('success', 'Address deleted successfully.');
            } else {
                session()->setFlashdata('error', 'Failed to delete address.');
            }
        } else {
            session()->setFlashdata('error', 'Address not found.');
        }
        return redirect()->to('/addresses');
    }
}
