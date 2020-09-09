<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 23/4/20
 * Time: 12:21 PM
 */

namespace App\Http\Controllers\Admin;


use App\Enums\UpdateStatus;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\Controller;
use App\Libraries\MyString;
use App\Libraries\MyUpdate;
use App\Models\Update;
use Carbon\Carbon;
use http\Env\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;


class UpdateController extends BackendController
{
    protected $updateVersion;
    protected $fileWithPath;
    protected $fileExtractPath;
    protected $fileExtractPathWithVersionFolder;
    protected $rootPath;

    public function index()
    {
        $this->data['sitetitle'] = 'Updates';
        return view('admin.update.index', $this->data);
    }

    public function checking()
    {
        $array = [
            'version' => 'none',
            'status'  => false,
            'message' => 'You are using latest version'
        ];

        if ( $postData = @MyUpdate::postData() ) {
            $versionChecking = MyUpdate::versionChecking($postData);
            if ( $versionChecking->status ) {
                if ( $versionChecking->version != 'none' ) {
                    Session::put('updateVersion', $versionChecking->version);
                    $array = [
                        'version' => $versionChecking->version,
                        'status'  => true,
                        'message' => 'Update available, do you want to update ' . $versionChecking->version . ' ?'
                    ];
                }
            }
        }
        return json_encode($array);
    }

    public function update()
    {
        try {
            $response = [ 'status' => false ];
            $version  = Session::get('updateVersion');
            if ( $version && $version != 'none' ) {
                $this->updateVersion                    = $version;
                $this->fileWithPath                     = MyUpdate::setSlash(config('site.file_extract_path')) . $this->updateVersion . '.zip';
                $this->fileExtractPath                  = MyUpdate::setSlash(config('site.file_extract_path'));
                $this->rootPath                         = MyString::strReplaceEnd('public/', '', MyString::strReplaceEnd('public', '', config('site.root_path')));
                $this->fileExtractPathWithVersionFolder = $this->fileExtractPath . $this->updateVersion;
                $fileDownload                           = MyUpdate::fileDownload($version, $this->fileExtractPath);

                if ( $fileDownload->status ) {
                    if ( File::exists($this->fileWithPath) ) {
                        if ( MyUpdate::fileUnZip($this->fileWithPath, $this->fileExtractPath)->status ) {
                            if ( MyUpdate::fileManager($this->fileExtractPathWithVersionFolder,
                                $this->rootPath)->status ) {
                                MyUpdate::migration('update.json', $this->fileExtractPathWithVersionFolder);
                                $version = MyUpdate::updateJson('update.json',
                                    $this->fileExtractPathWithVersionFolder)->data->version;
                                if ( $version != 'none' ) {
                                    Update::create([
                                        'version' => MyUpdate::updateJson('update.json',
                                            $this->fileExtractPathWithVersionFolder)->data->version,
                                        'date'    => Carbon::parse()->format('Y-m-d H:i:s'),
                                        'status'  => UpdateStatus::SUCCESS,
                                        'log'     => MyUpdate::updateLog('update.log',
                                            $this->fileExtractPathWithVersionFolder)->data
                                    ]);
                                }
                                MyUpdate::deleteZipWithFile($this->updateVersion, $this->fileExtractPath);
                                Session::remove('updateVersion');

                                $response            = [ 'status' => true ];
                                $response['message'] = 'Success';
                            } else {
                                $response['message'] = 'The update zip does not found';
                            }
                        } else {
                            $response['message'] = 'The update zip does not found';
                        }
                    } else {
                        $response['message'] = 'The download file does not found';
                    }
                } else {
                    $response['message'] = $fileDownload->message;
                }
            }

            return json_encode($response);
        } catch ( \Exception $exception ) {
            return [ 'status' => true, 'message' => $exception->getMessage() ];
        }
    }

    public function getUpdates()
    {
        $updates     = Update::orderBy('id', 'desc')->get();
        $updateArray = [];

        $i = 1;
        if ( !blank($updates) ) {
            foreach ( $updates as $update ) {
                $updateArray[ $i ]          = $update;
                $updateArray[ $i ]['setID'] = $i;
                $i++;
            }
        }

        return Datatables::of($updateArray)
            ->addColumn('action', function ($update) {
                return '<button data-content="'.$update->id.'" data-url="'.route('admin.updates.update-log').'" class="btn btn-sm btn-icon btn-info update-view trigger--fire-modal-1" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></button>';
            })
            ->editColumn('status', function ( $update ) {
                return trans('update_status.' . $update->status);
            })
            ->editColumn('created_at', function ( $update ) {
                return Carbon::parse($update->created_at)->format('d M Y, h:i A');
            })
            ->make(true);
    }

    public function log()
    {
        $update = Update::findOrFail($_REQUEST['id']);
        if(!blank($update)) {
            return json_encode(['status' => true, 'log' => $update->log]);
        }
        return json_encode(['status' => false, 'log' => 'data not found']);
    }
}