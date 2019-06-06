<?php

namespace Akeneo\Bundle\AssetCodeModificationBundle\Controller\Rest;

use Akeneo\Asset\Bundle\Controller\Rest\MassUploadController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset mass upload controller - modifying behavior of Akeneo\Asset\Bundle\Controller\Rest\MassUploadController
 *
 * @author Leo Thibaudat <leo.thibaudat@akeneo.com>
 */
class AssetCodeModificationMassUploadController extends MassUploadController
{

    /**
     * function: uploadAction
     *           extends the original function to generate a
     *           filename with random numbers at the end
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function uploadAction(Request $request)
    {
        $response = new JsonResponse();
        $files = $request->files;
        $uploaded = null;

        if ($files->count() > 0) {
            $file = $files->getIterator()->current();
            $originalFilename = $file->getClientOriginalName();
            $originalFilename = basename(trim($originalFilename));

            $newFileName = pathinfo($originalFilename, PATHINFO_FILENAME) . "_" . random_int(0, 100000) . "." . pathinfo($originalFilename, PATHINFO_EXTENSION);

            $parsedFilename = $this->uploadChecker->getParsedFilename($newFileName);
            $targetDir = $this->getUploadContext()->getTemporaryUploadDirectory();
            $uploaded = $file->move($targetDir, $parsedFilename->getRawFilename());

            $response->setStatusCode(Response::HTTP_OK);
            $response->setData([
                'success' => 'This file has been renamed to: ' . $newFileName
            ]);
        }

        if (null === $uploaded) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'error' => 'pimee_product_asset.mass_upload.error.upload'
            ]);
        }

        return $response;
    }
}