<?php

namespace Janchris80\DriveFiles\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Janchris80\DriveFiles\Actions\CompleteUploadAction;
use Janchris80\DriveFiles\Actions\CreateUploadSessionAction;
use Janchris80\DriveFiles\Actions\DeleteDriveFileAction;
use Janchris80\DriveFiles\Actions\RevokeDriveShareAction;
use Janchris80\DriveFiles\Actions\ShareDriveFileAction;
use Janchris80\DriveFiles\Http\Requests\CompleteUploadRequest;
use Janchris80\DriveFiles\Http\Requests\CreateUploadSessionRequest;
use Janchris80\DriveFiles\Http\Requests\ListDriveFilesRequest;
use Janchris80\DriveFiles\Http\Requests\RevokeDriveShareRequest;
use Janchris80\DriveFiles\Http\Requests\ShareDriveFileRequest;
use Janchris80\DriveFiles\Http\Resources\DriveFileDetailResource;
use Janchris80\DriveFiles\Http\Resources\DriveFileListResource;
use Janchris80\DriveFiles\Models\DriveFile;
use Janchris80\DriveFiles\Queries\DriveFileQuery;
use Janchris80\DriveFiles\Services\GoogleDriveService;

class DriveFileController extends Controller
{
    public function __construct(private readonly GoogleDriveService $drive)
    {
    }

    public function index(ListDriveFilesRequest $request)
    {
        return DriveFileListResource::collection(
            (new DriveFileQuery($request))->paginate()
        );
    }

    public function store(CreateUploadSessionRequest $request, CreateUploadSessionAction $action): JsonResponse
    {
        return response()->json(
            $action->execute($request->validated(), $request->user())
        );
    }

    public function complete(CompleteUploadRequest $request, CompleteUploadAction $action): DriveFileDetailResource
    {
        return new DriveFileDetailResource(
            $action->execute($request->validated(), $request->user())
        );
    }

    public function show(DriveFile $driveFile): DriveFileDetailResource
    {
        return new DriveFileDetailResource($driveFile);
    }

    public function preview(DriveFile $driveFile)
    {
        return redirect()->away($this->drive->getPreviewUrl($driveFile->google_file_id));
    }

    public function download(DriveFile $driveFile)
    {
        return redirect()->away($this->drive->getTemporaryUrl($driveFile->google_file_id, 3600));
    }

    public function destroy(DriveFile $driveFile, DeleteDriveFileAction $action): JsonResponse
    {
        $action->execute($driveFile);
        return response()->json(['message' => 'Deleted']);
    }

    public function share(DriveFile $driveFile, ShareDriveFileRequest $request, ShareDriveFileAction $action): DriveFileDetailResource
    {
        return new DriveFileDetailResource($action->execute($driveFile));
    }

    public function revokeShare(DriveFile $driveFile, RevokeDriveShareRequest $request, RevokeDriveShareAction $action): DriveFileDetailResource
    {
        return new DriveFileDetailResource($action->execute($driveFile));
    }
}
