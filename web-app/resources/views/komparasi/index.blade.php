@extends('layouts.app')

@section('title', 'Komparasi Random Forest vs Naïve Bayes - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="card bg-warning-subtle text-warning border-0 shadow-none">
      <div class="card-body p-4">
        <div class="d-md-flex align-items-center justify-content-between">
          <div>
            <h4 class="fw-bold mb-1 text-dark"><i class="ti ti-git-compare me-2"></i>Komparasi Algoritma Random Forest &amp; Naïve Bayes</h4>
            <p class="mb-0 text-muted">Evaluasi performa klasifikasi menggunakan matriks Confusion Matrix (Akurasi, Presisi, Recall, dan F1-Score).</p>
          </div>
          <div class="mt-3 mt-md-0">
            <span class="badge bg-primary px-3 py-2 fs-3">
              Rekomendasi Terbaik: <strong>{{ str_replace('_', ' ', $winner) }}</strong>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Comparison Side-by-Side Cards -->
  <div class="col-lg-6">
    <div class="card border {{ $rfModel && $rfModel->is_active ? 'border-primary border-2 shadow' : '' }}">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
          <h5 class="fw-bold mb-0 text-primary">1. Algoritma Random Forest (Metode Utama)</h5>
          <small class="text-muted">Ensemble Trees &amp; Majority Voting</small>
        </div>
        @if($rfModel && $rfModel->is_active)
          <span class="badge bg-primary">Model Aktif</span>
        @else
          <form action="{{ route('komparasi.set-active') }}" method="POST">
            @csrf
            <input type="hidden" name="algoritma" value="Random_Forest">
            <button type="submit" class="btn btn-sm btn-outline-primary">Jadikan Aktif</button>
          </form>
        @endif
      </div>
      <div class="card-body">
        @if($rfModel)
          <div class="row text-center mb-4">
            <div class="col-3 border-end">
              <span class="text-muted fs-2">Akurasi</span>
              <h3 class="fw-bolder text-primary mb-0">{{ $rfModel->akurasi }}%</h3>
            </div>
            <div class="col-3 border-end">
              <span class="text-muted fs-2">Presisi</span>
              <h3 class="fw-bolder text-success mb-0">{{ $rfModel->presisi }}%</h3>
            </div>
            <div class="col-3 border-end">
              <span class="text-muted fs-2">Recall</span>
              <h3 class="fw-bolder text-info mb-0">{{ $rfModel->recall }}%</h3>
            </div>
            <div class="col-3">
              <span class="text-muted fs-2">F1-Score</span>
              <h3 class="fw-bolder text-danger mb-0">{{ $rfModel->f1_score }}%</h3>
            </div>
          </div>

          <h6 class="fw-bold mb-2">Confusion Matrix (Data Uji 20%):</h6>
          <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-light">
                <tr>
                  <th rowspan="2">Aktual</th>
                  <th colspan="2">Prediksi Model</th>
                </tr>
                <tr>
                  <th class="text-danger">Tinggi</th>
                  <th class="text-success">Rendah</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th class="table-light text-danger">Tinggi</th>
                  <td class="bg-danger-subtle fw-bold fs-4">{{ $rfModel->confusion_matrix['tp'] ?? 0 }} (TP)</td>
                  <td class="bg-light">{{ $rfModel->confusion_matrix['fn'] ?? 0 }} (FN)</td>
                </tr>
                <tr>
                  <th class="table-light text-success">Rendah</th>
                  <td class="bg-light">{{ $rfModel->confusion_matrix['fp'] ?? 0 }} (FP)</td>
                  <td class="bg-success-subtle fw-bold fs-4">{{ $rfModel->confusion_matrix['tn'] ?? 0 }} (TN)</td>
                </tr>
              </tbody>
            </table>
          </div>
          <small class="text-muted">Hyperparameter: n_estimators={{ $rfModel->hyperparameter_settings['n_estimators'] ?? 30 }}, max_depth={{ $rfModel->hyperparameter_settings['max_depth'] ?? 6 }}, split=80:20</small>
        @else
          <p class="text-center text-muted py-4">Model belum dilatih. Silakan jalankan mining pipeline terlebih dahulu.</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card border {{ $nbModel && $nbModel->is_active ? 'border-primary border-2 shadow' : '' }}">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
          <h5 class="fw-bold mb-0 text-success">2. Algoritma Naïve Bayes (Metode Komparasi)</h5>
          <small class="text-muted">Gaussian Probability &amp; Bayes Theorem</small>
        </div>
        @if($nbModel && $nbModel->is_active)
          <span class="badge bg-primary">Model Aktif</span>
        @else
          <form action="{{ route('komparasi.set-active') }}" method="POST">
            @csrf
            <input type="hidden" name="algoritma" value="Naive_Bayes">
            <button type="submit" class="btn btn-sm btn-outline-primary">Jadikan Aktif</button>
          </form>
        @endif
      </div>
      <div class="card-body">
        @if($nbModel)
          <div class="row text-center mb-4">
            <div class="col-3 border-end">
              <span class="text-muted fs-2">Akurasi</span>
              <h3 class="fw-bolder text-primary mb-0">{{ $nbModel->akurasi }}%</h3>
            </div>
            <div class="col-3 border-end">
              <span class="text-muted fs-2">Presisi</span>
              <h3 class="fw-bolder text-success mb-0">{{ $nbModel->presisi }}%</h3>
            </div>
            <div class="col-3 border-end">
              <span class="text-muted fs-2">Recall</span>
              <h3 class="fw-bolder text-info mb-0">{{ $nbModel->recall }}%</h3>
            </div>
            <div class="col-3">
              <span class="text-muted fs-2">F1-Score</span>
              <h3 class="fw-bolder text-danger mb-0">{{ $nbModel->f1_score }}%</h3>
            </div>
          </div>

          <h6 class="fw-bold mb-2">Confusion Matrix (Data Uji 20%):</h6>
          <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
              <thead class="table-light">
                <tr>
                  <th rowspan="2">Aktual</th>
                  <th colspan="2">Prediksi Model</th>
                </tr>
                <tr>
                  <th class="text-danger">Tinggi</th>
                  <th class="text-success">Rendah</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th class="table-light text-danger">Tinggi</th>
                  <td class="bg-danger-subtle fw-bold fs-4">{{ $nbModel->confusion_matrix['tp'] ?? 0 }} (TP)</td>
                  <td class="bg-light">{{ $nbModel->confusion_matrix['fn'] ?? 0 }} (FN)</td>
                </tr>
                <tr>
                  <th class="table-light text-success">Rendah</th>
                  <td class="bg-light">{{ $nbModel->confusion_matrix['fp'] ?? 0 }} (FP)</td>
                  <td class="bg-success-subtle fw-bold fs-4">{{ $nbModel->confusion_matrix['tn'] ?? 0 }} (TN)</td>
                </tr>
              </tbody>
            </table>
          </div>
          <small class="text-muted">Distribusi: Gaussian (Continuous Features), split=80:20</small>
        @else
          <p class="text-center text-muted py-4">Model belum dilatih. Silakan jalankan mining pipeline terlebih dahulu.</p>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Bar Chart Komparasi Metrik -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Grafik Perbandingan Metrik Evaluasi (Random Forest vs Naïve Bayes)</h5>
        <div id="chart-komparasi" style="min-height: 300px;"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var rfAkurasi = {{ $rfModel->akurasi ?? 0 }};
    var rfPresisi = {{ $rfModel->presisi ?? 0 }};
    var rfRecall = {{ $rfModel->recall ?? 0 }};
    var rfF1 = {{ $rfModel->f1_score ?? 0 }};

    var nbAkurasi = {{ $nbModel->akurasi ?? 0 }};
    var nbPresisi = {{ $nbModel->presisi ?? 0 }};
    var nbRecall = {{ $nbModel->recall ?? 0 }};
    var nbF1 = {{ $nbModel->f1_score ?? 0 }};

    var options = {
      series: [{
        name: 'Random Forest',
        data: [rfAkurasi, rfPresisi, rfRecall, rfF1]
      }, {
        name: 'Naïve Bayes',
        data: [nbAkurasi, nbPresisi, nbRecall, nbF1]
      }],
      chart: {
        type: 'bar',
        height: 320,
        toolbar: { show: false }
      },
      colors: ['#5d87ff', '#13deb9'],
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '40%',
          borderRadius: 4
        },
      },
      dataLabels: { enabled: true, formatter: function(val) { return val + "%"; } },
      stroke: { show: true, width: 2, colors: ['transparent'] },
      xaxis: {
        categories: ['Akurasi', 'Presisi', 'Recall', 'F1-Score'],
      },
      yaxis: {
        max: 100,
        title: { text: 'Persentase (%)' }
      },
      fill: { opacity: 1 },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + " %";
          }
        }
      }
    };

    var chart = new ApexCharts(document.querySelector("#chart-komparasi"), options);
    chart.render();
  });
</script>
@endpush
