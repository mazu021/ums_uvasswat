<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\LabEquipment;
use App\Models\Student;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class CampusServicesController extends Controller
{
    public function library(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $query = Book::query();
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('author', 'like', "%{$request->search}%")
                  ->orWhere('isbn', 'like', "%{$request->search}%");
        }

        $books = $query->paginate($perPage);
        $recentIssues = BookIssue::with(['book', 'student.user'])->latest()->take(10)->get();

        return view('campus_services.library', compact('books', 'recentIssues'));
    }

    public function hostel()
    {
        $hostels = Hostel::with('rooms')->get();
        return view('campus_services.hostel', compact('hostels'));
    }

    public function transport()
    {
        $routes = TransportRoute::all();
        return view('campus_services.transport', compact('routes'));
    }

    public function inventory(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $equipment = LabEquipment::with('department')->latest()->paginate($perPage);
        return view('campus_services.inventory', compact('equipment'));
    }
}
