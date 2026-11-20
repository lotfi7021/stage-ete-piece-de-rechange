@extends('back.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/back/css/rm-calendar.css') }}">
@endpush

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Mon calendrier</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('admin') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Mon calendrier</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card rm-calendar">
                <div class="card-header rm-cal-header">
                    <h3 class="card-title">{{ ucfirst($monthStart->translatedFormat('F Y')) }}</h3>
                    <div class="card-tools rm-cal-nav">
                        <a href="{{ route('livreur.calendar', ['month' => $moisPrecedent]) }}"
                           class="btn btn-sm btn-outline-secondary" title="Mois précédent">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="{{ route('livreur.calendar') }}" class="btn btn-sm btn-outline-primary">
                            Aujourd'hui
                        </a>
                        <a href="{{ route('livreur.calendar', ['month' => $moisSuivant]) }}"
                           class="btn btn-sm btn-outline-secondary" title="Mois suivant">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="rm-cal-grid">
                            <thead>
                                <tr>
                                    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $libelle)
                                        <th scope="col">{{ $libelle }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_chunk($jours, 7) as $semaine)
                                    <tr>
                                        @foreach($semaine as $jour)
                                            @php
                                                $jourCommandes = $parJour->get($jour->toDateString(), collect());
                                                $classes = collect([
                                                    'rm-cal-day',
                                                    $jour->isSameMonth($monthStart) ? null : 'is-other-month',
                                                    $jour->isToday() ? 'is-today' : null,
                                                    $jour->isWeekend() ? 'is-weekend' : null,
                                                    $jourCommandes->isEmpty() ? null : 'has-events',
                                                ])->filter()->implode(' ');
                                            @endphp
                                            <td class="{{ $classes }}">
                                                <div class="rm-cal-daynum">
                                                    <span>{{ $jour->day }}</span>
                                                    @if($jour->isToday())
                                                        <span class="rm-cal-today-tag">Aujourd'hui</span>
                                                    @endif
                                                </div>

                                                @if($jourCommandes->isNotEmpty())
                                                    <div class="rm-cal-events">
                                                        @foreach($jourCommandes as $commande)
                                                            @php
                                                                $etatClass = match ($commande->etat) {
                                                                    'en attente' => 'attente',
                                                                    'confirmée' => 'confirmee',
                                                                    'Livrée' => 'livree',
                                                                    default => 'autre',
                                                                };
                                                            @endphp
                                                            <a href="{{ route('admin.list_commandeM.facture', $commande->id) }}"
                                                               class="rm-cal-event rm-cal-event--{{ $etatClass }}"
                                                               title="{{ $commande->ref_commande }} — {{ $commande->etat }}">
                                                                <span class="rm-cal-ref">{{ $commande->ref_commande }}</span>
                                                                <span class="rm-cal-client">{{ $commande->client?->nom ?? '—' }}</span>
                                                                <span class="rm-cal-price">{{ number_format($commande->prix, 2) }} DT</span>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer rm-cal-footer">
                    <div class="rm-cal-legend">
                        <span class="rm-cal-legend-label">Légende :</span>
                        <span class="badge bg-warning">En attente</span>
                        <span class="badge bg-info">Confirmée</span>
                        <span class="badge bg-success">Livrée</span>
                    </div>
                    <div class="rm-cal-total">
                        @php $totalMois = array_sum($compteurs); @endphp
                        @if($totalMois > 0)
                            {{ $totalMois }} livraison{{ $totalMois > 1 ? 's' : '' }}
                            en {{ $monthStart->translatedFormat('F Y') }}
                        @else
                            Aucune livraison ce mois-ci.
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@stop
