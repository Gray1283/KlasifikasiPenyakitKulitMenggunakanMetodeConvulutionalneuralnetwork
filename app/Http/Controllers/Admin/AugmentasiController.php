<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MLService;
use Illuminate\Http\Request;

class AugmentasiController extends Controller
{
    protected MLService $ml;

    public function __construct(MLService $ml)
    {
        $this->ml = $ml;
    }

    /**
     * Tampilkan halaman Augmentasi - termasuk info jumlah gambar per kelas
     */
    public function index()
    {
        $datasetInfo = $this->ml->getDatasetInfo();
        $dataset     = $datasetInfo['success'] ? ($datasetInfo['data']['dataset'] ?? []) : [];

        $kelasList = [
            'mel'   => 'Melanoma',
            'nv'    => 'Melanocytic Nevi',
            'bcc'   => 'Basal Cell Carcinoma',
            'akiec' => 'Actinic Keratoses',
            'bkl'   => 'Benign Keratosis',
            'df'    => 'Dermatofibroma',
            'vasc'  => 'Vascular Lesions',
        ];

        return view('admin.augmentasi.index', [
            'dataset'   => $dataset,
            'kelasList' => $kelasList,
            'flaskOnline' => $this->ml->isHealthy(),
        ]);
    }

    /**
     * Proses augmentasi untuk 1 kelas - dipanggil via AJAX dari halaman index
     */
    public function process(Request $request)
    {
        $request->validate([
            'label'  => 'required|string|in:mel,nv,bcc,akiec,bkl,df,vasc',
            'jumlah' => 'required|integer|min:1|max:2000',
        ]);

        $result = $this->ml->augmentClass(
            $request->input('label'),
            (int) $request->input('jumlah')
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Augmentasi gagal',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data'    => $result['data'],
        ]);
    }
}