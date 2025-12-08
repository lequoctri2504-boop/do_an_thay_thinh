<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Hiển thị danh sách tất cả bài viết đã xuất bản
     */
    public function index(Request $request)
    {
        $query = BaiViet::where('trang_thai', 'XUAT_BAN')
            ->whereNull('deleted_at');
            
        $keyword = $request->input('q');
        $sortBy = $request->input('sort', 'newest'); 

        // 1. Logic Tìm kiếm
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('tieu_de', 'like', "%{$keyword}%")
                  ->orWhere('noi_dung', 'like', "%{$keyword}%");
            });
        }
        
        // 2. Logic Sắp xếp
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'popular':
                $query->orderBy('luot_xem', 'desc'); 
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $newsArticles = $query->paginate(10);
        // Đảm bảo các tham số tìm kiếm/sắp xếp được giữ lại khi phân trang
        $newsArticles->appends($request->all()); 
            
        return view('news.index', compact('newsArticles', 'keyword', 'sortBy'));
    }
    
    /**
     * Hiển thị chi tiết bài viết
     */
    public function show($slug)
    {
        $article = BaiViet::where('slug', $slug)
            ->where('trang_thai', 'XUAT_BAN')
            ->whereNull('deleted_at')
            ->firstOrFail();
            
        // Tăng lượt xem 
        $article->increment('luot_xem');
        
        // Lấy bài viết liên quan
        $relatedArticles = BaiViet::where('trang_thai', 'XUAT_BAN')
            ->where('id', '!=', $article->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        return view('news.show', compact('article', 'relatedArticles'));
    }
}