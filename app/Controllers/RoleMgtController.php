<?php

namespace App\Controllers;

use App\Models\RoleMgtModel;
use App\Models\UserGroupModel;

class RoleMgtController extends BaseController
{

    function index()
    {

        $roleMgtModel = new RoleMgtModel();
        $data['user_data']  = $roleMgtModel->join('user_group', 'user_group.id = roleMgt_table.user_role_id')
            ->select('roleMgt_table.*')
            ->select('user_group.user_role')
            ->orderBy('roleMgt_table.user_role_id', 'ASE')
            ->orderBy('roleMgt_table.pdf_File', 'ASE')
            ->whereNotIn('roleMgt_table.user_role_id', [0])
            ->paginate(10);
        $data['pagi_link'] = $roleMgtModel->pager;

        return view('roleManagment/role_view', $data);
    }

    function add()
    {
        $userGrp = new UserGroupModel();
        $data['user_grp_data'] = $userGrp->orderBy('id', 'ASE')->findAll();

        return view('roleManagment/add_data', $data);
    }

    function add_validation()
    {
        helper(['form', 'url']);


        $error = $this->validate([
            'name'    =>    'required|min_length[3]',
            'email'    =>    'required|valid_email',
            'gender' =>    'required',
            'pdf_File' => 'uploaded[pdf_File]'

        ]);

        if (!$error) {
            echo view('add_data', [
                'error'     => $this->validator
            ]);
        } else {

            $roleMgtModel = new RoleMgtModel();

            $usergrpId = $this->request->getVar('usergrpselect');

            //File store in writable/uploads/pdfFile/
            $pdfFile = $this->request->getFile('pdf_File');        //$pdfFileOldName = $pdfFile->getName();
            $pdfFileNewName = $pdfFile->getRandomName();
            $pdfFile->store('pdfFile/', $pdfFileNewName);

            $roleMgtModel->save([
                'user_role_id' => $usergrpId,
                'name'    =>    $this->request->getVar('name'),
                'email'    =>    $this->request->getVar('email'),
                'gender' =>    $this->request->getVar('gender'),
                'pdf_File' => $pdfFileNewName
            ]);

            $session = \Config\Services::session();

            $session->setFlashdata('success', 'User Data Added');

            return $this->response->redirect(site_url('rolemanagment'));
        }
    }


    // show single user
    function fetch_single_data($id = null)
    {
        $roleMgtModel = new RoleMgtModel();

        $data['user_data'] = $roleMgtModel->where('id', $id)->first();

        return view('roleManagment/edit_data', $data);
    }

    function edit_validation()
    {
        helper(['form', 'url']);

        $error = $this->validate([
            'name'     => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'gender' => 'required'
        ]);

        $roleMgtModel = new RoleMgtModel();

        $id = $this->request->getVar('id');

        if (!$error) {
            $data['user_data'] = $roleMgtModel->where('id', $id)->first();
            $data['error'] = $this->validator;
            echo view('edit_data', $data);
        } else {
            $data = [
                'name' => $this->request->getVar('name'),
                'email'  => $this->request->getVar('email'),
                'gender'  => $this->request->getVar('gender'),
            ];

            $roleMgtModel->update($id, $data);

            $session = \Config\Services::session();

            $session->setFlashdata('success', 'User Data Updated');

            return $this->response->redirect(site_url('rolemanagment'));
        }
    }


    function delete($id)
    {
        $roleMgtModel = new RoleMgtModel();

        $roleMgtModel->where('id', $id)->delete($id);

        $session = \Config\Services::session();

        $session->setFlashdata('success', 'User Data Deleted');

        return $this->response->redirect(site_url('rolemanagment'));
    }

    //Download PDF File
    function pdfDownload()
    {
        if ($this->request->isAJAX()) {
            $id = $this->request->getGet('id');

            $roleMgtModel = new RoleMgtModel();

            $data = $roleMgtModel->asArray()
                ->where('id', $id)
                ->select('roleMgt_table.pdf_File, roleMgt_table.name')
                ->first();

            return $this->response->download('writable/uploads/pdfFile/' . $data['pdf_File'] . '', null)->setFileName($data['name'] . '.pdf');
        }
    }
}
