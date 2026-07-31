<?php

namespace App\Http\Controllers\Api\Reels;

use App\Http\Controllers\Controller;
use App\Models\Reels\DramaSeries;
use Illuminate\Http\Request; 
use Validator;

class SearchController extends Controller
{
    public function index(Request $request): array
    {
        $validation = Validator::make($request->all(),[
            'user_id' => 'required|exists:user,id',
        ]);

        if ($validation->fails()) {
            $data['status'] = 400;
            $data['message'] = __('api_msg.please_enter_required_fields');
            return $data;
        }
        
        $query = trim((string) $request->query('q', ''));

        return [
            'data' => DramaSeries::query()
                ->where('status', 'published')
                ->when($query !== '', function ($builder) use ($query): void {
                    $builder->where(function ($inner) use ($query): void {
                        $inner
                            ->where('title', 'like', "%{$query}%")
                            ->orWhere('genre', 'like', "%{$query}%")
                            ->orWhere('language', 'like', "%{$query}%");
                    });
                })
                ->orderByDesc('rating')
                ->take(30)
                ->get(),
        ];
    }
}
