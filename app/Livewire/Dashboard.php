<?php

namespace App\Livewire;

use App\Models\metrics;
use App\Models\shops;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
        use WithPagination;

    public $shops;
    public $metrics;

     public $dateFrom;
 
     public $dateTo ;
 
     public $leaderboard = [];
    public $selectedShop = 'all';

    public $gmvUsd = 0;
    public $followers = 0;
    public $ctor = 0;

      public $sortField = 'date';    // default sort
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind'; // or 'tailwind' depending on your CSS

      public $labels = [];
    public $data = [];

    public function mount()
    {
        $this->dateFrom = now()->toDateString();
        $this->dateTo = now()->toDateString();
        $this->shops = shops::all();
        $this->metrics = metrics::whereBetween('date', [$this->dateFrom, $this->dateTo])->get();
    }
    public function render()
    {
        $paginated_metrics = [];
        $metrics = [];
        $leaderboardmetrics = [];
        if($this->selectedShop != 'all'){
            $this->metrics = metrics::where('shop_id', $this->selectedShop)
                ->whereBetween('date', [$this->dateFrom, $this->dateTo])
                ->get();

            $paginated_metrics = metrics::where('shop_id', $this->selectedShop)
                ->whereBetween('date', [$this->dateFrom, $this->dateTo])
           ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);

             $metrics = metrics::selectRaw('date, SUM(gmv_usd) as total_gmv')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        } else {
            $this->metrics = metrics::whereBetween('date', [$this->dateFrom, $this->dateTo])
                ->get();

                      $paginated_metrics = metrics::whereBetween('date', [$this->dateFrom, $this->dateTo])
      ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);

             $metrics = metrics::selectRaw('date, SUM(gmv_usd) as total_gmv')->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        }
        $leaderboardmetrics= metrics::whereBetween('date', [$this->dateFrom, $this->dateTo])
                ->get();

 // Query metrics between dates
        $metrics = metrics::with('shop')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->orderBy('date')
            ->get()
            ->groupBy(groupBy: 'shop.shop_name');

        // Unique labels (dates)
        $labels = metrics::whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->orderBy('date')
            ->distinct()
            ->pluck('date');

      $colors = ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF', '#8BC34A', '#FF9800', '#9C27B0'];

        $datasets = [];
        $shopIndex = 0;

        foreach ($metrics as $shopName => $shopMetrics) {
              $datasets[] = [
        'label' => $shopName,
        'data' => $labels->map(function ($date) use ($shopMetrics) {
            return optional($shopMetrics->firstWhere('date', $date))->gmv_usd ?? 0;
        }),
        'backgroundColor' => $colors[$shopIndex % count($colors)], // ✅ ensures unique assignment
    ];

    $shopIndex++;
        }


        $this->gmvUsd = $this->metrics->sum('gmv_usd');
        $this->followers = $this->metrics->sum('followers');
        $this->ctor = $this->metrics->avg('ctor');

      $this->leaderboard = $leaderboardmetrics
    ->groupBy('shop_id')
    ->map(function ($group) {
        $shop = $group->first()->shop;

        // Aggregate values for this shop
        $gmvUsd = $group->sum('gmv_usd');
        $followers = $group->sum('followers');
        $ctor = $group->avg('ctor'); // avg makes more sense for rates

        // Calculate points
        $points = ($gmvUsd / 10) + ($followers * 0.5) + ($ctor * 100);

        return [
            'shop_name' => $shop->shop_name,
            'gmv_usd' => $gmvUsd,
            'followers' => $followers,
            'ctor' => $ctor,
            'points' => $points,
        ];
    })
    ->sortByDesc('points') // sort by points instead of GMV
    ->take(5)              // top 5 shops
    ->values()
    ->toArray();
$this->dispatch('refreshChart', labels: $labels, datasets: $datasets);

        return view('livewire.dashboard',['paginated_metrics' => $paginated_metrics, 'labels' => $labels,
            'datasets' => $datasets,]);
    }

    public function applyFilters()
    {
        $this->render();
    }

     public function sortBy($field)
    {
        if ($this->sortField === $field) {
            // toggle between asc/desc
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
}
