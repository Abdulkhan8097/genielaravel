<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileUploadController extends Controller
{
    /**
     * Author: Prasad Wargad
     * Purpose: Storing uploaded files in file path specified at runtime by user or else default location will be "app/public" from storage folder
     * Created: 08/08/2021
     * Modified:
     * Modified by:
     */
    public function adminfileuploads(Request $request){
        /* Input parameters: 01) upload_path: File uploading folder path
         * 02) allowed_types: Stores list of files allowed to be uploaded
         * 03) max_size: Maximum file size allowed for uploading a file
         * 04) saving_file_name: If mentioned file gets saved with this name other wise original file name used for storing file
         */
        $file_validations = 'required|file';

        // getting details of file uploading folder
        if($request->input('upload_path') !== null && !empty($request->input('upload_path'))){
            // file upload location given
            $file_upload_folder = storage_path($request->input('upload_path'));
        }
        else{
            // default file upload location
            $file_upload_folder = storage_path('app/public/');
        }

        // checking whether allowed_types mentioned or not
        if($request->input('allowed_types') !== null && !empty($request->input('allowed_types'))){
            $file_validations .= '|mimetypes:'. trim($request->input('allowed_types'));
        }

        // checking whether any max size (in MB) for file is mentioned or not
        if($request->input('max_size') !== null && !empty($request->input('max_size')) && is_numeric($request->input('max_size'))){
            $file_validations .= '|max:'. intval($request->input('max_size'));
        }

        // checking whether any filename given or not for saving the file, if yes using the given name else using original file name
        if($request->input('saving_file_name') !== null && !empty($request->input('saving_file_name'))){
            $saving_file_name = $request->input('saving_file_name');
        }
        else{
            $saving_file_name = $file->getClientOriginalName();
        }

        // checking request is coming from valid domain or not
        $request_referer = request()->server('HTTP_REFERER');
        if($request_referer === null || ($request_referer !== null && stripos($request_referer, 'samcomf.com') === FALSE && stripos($request_referer, env('APP_URL')) === FALSE)){
            $response = [
                'status' => 'error',
                'message' => 'Validation Errors',
                'data' => array('errors' => 'Request not allowed')
            ];
            return response()->json($response, 200);
        }

        $validator = Validator::make($request->all(), [
            'file' => $file_validations,
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            $data['errors'] = $errors->all();
            $response = [
                'status' => 'error',
                'message' => 'Validation Errors',
                'data' => $data
            ];
            return response()->json($response, 200);
        }

        $file = $request->file('file');
        $file->move($file_upload_folder, $saving_file_name);
        $success_arr = array('status' => 'success' ,'message' => 'File uploaded successfully');
        return response()->json($success_arr, 200);
    }

    /**
     * Author: Prasad Wargad
     * Purpose: Removing uploaded files in file path specified
     * Created: 13/08/2021
     * Modified:
     * Modified by:
     */
    public function adminfiledelete(Request $request){
        /* Input parameters: 01) upload_path: File to be removed from folder path
         * 02) removing_file_name: filename specified for removal from physical folder
         */
        $err_flag = 0;          // err_flag is 0 means no error
        $err_msg = array();     // err_msg stores list of errors found during process

        // getting details of file uploading folder
        if($request->input('upload_path') !== null && !empty($request->input('upload_path'))){
            // file upload location given
            $file_upload_folder = storage_path($request->input('upload_path'));
        }
        else{
            $err_flag = 1;
            $err_msg[] = 'Required folder details not found';
        }

        // checking whether any filename given or not for saving the file, if yes using the given name else using original file name
        if($request->input('removing_file_name') !== null && !empty($request->input('removing_file_name'))){
            $removing_file_name = $request->input('removing_file_name');
        }
        else{
            $err_flag = 1;
            $err_msg[] = 'File name not found';
        }

        // checking request is coming from valid domain or not
        $request_referer = request()->server('HTTP_REFERER');
        if($request_referer === null || ($request_referer !== null && stripos($request_referer, 'samcomf.com') === FALSE && stripos($request_referer, env('APP_URL')) === FALSE)){
            $err_flag = 1;
            $err_msg[] = 'Request not allowed';
        }

        // checking whether file exists or not
        if($err_flag == 0 && !\File::exists($file_upload_folder . $removing_file_name)){
            $err_flag = 1;
            $err_msg[] = 'File not found';
        }

        if($err_flag == 1){
            $response = [
                'status' => 'error',
                'message' => 'Validation Errors',
                'data' => array('errors' => $err_msg)
            ];
            return response()->json($response, 200);
        }

        \File::delete($file_upload_folder . $removing_file_name);
        $success_arr = array('status' => 'success' ,'message' => 'File deleted successfully');
        return response()->json($success_arr, 200);
    }
}
