@layout("rf::home")

@section("rf_content")
<ul class="nav nav-tabs">
	@foreach($lieux as $l)
	<li class="dropdown">
		<a href="#tab-pane-{{$l->id}}" data-toggle="tab">{{$l->nom}}</a>
	</li>
	@endforeach
</ul>

<div class="tab-content">
	<div class="tab-pane fade in active">
		<p>Sélectionnez un lieu pour afficher les stocks en réserve et dans les frigos.</p>
		<p>Les vols ont été notés :
			<ul>
				@foreach($lieux as $l)
				@if(isset($temps_ecoule[$l->id]))
					<li><span class="label">{{$l->nom}}</span> il y a {{$temps_ecoule[$l->id]}}
						@if($vols_30d[$l->id] <= 0 )
						<p>{{Bootstrapper\Progress::warning_normal(0)}}</p>
						@else
						<p>{{Bootstrapper\Progress::warning_normal(100 * $vols_30d[$l->id] / ($vols_30d[$l->id]+$consos_30d[$l->id]))}}</p>
						@endif
					</li>
				@endif
				@endforeach
			</ul>
		</p>
	</div>
@foreach($lieux as $l)
<div class="tab-pane fade" id="tab-pane-{{$l->id}}">
<div class="accordion" id="accordion-{{$l->id}}">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-{{$l->id}}" href="#{{$l->id}}-stocks">Gestion des stocks</a>
    </div>
    <div id="{{$l->id}}-stocks" class="accordion-body collapse in">
      <div class="accordion-inner">
				<p><a href="commande" class="btn btn-primary">Passer une commande</a></p>
				<table class="table table-striped table-bordered">
				  <thead>
				    <tr>
				      <th>Produit</th>
				      <th>Quantité en réserve</th>
				    </tr>
				  </thead>
				  <tbody>
				    @foreach(Stockproduit::with('produit')->where_lieu_id($l->id)->get() as $sp)
				      <tr>
								<td><a href="p/{{$sp->produit->id}}">{{$sp->produit->nom}}</a></td>
								<td>{{$sp->qte_reserve}}</td>
				      </tr>
				    @endforeach
				  </tbody>
				</table>
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-{{$l->id}}" href="#{{$l->id}}-frigos">Frigos</a>
    </div>
    <div id="{{$l->id}}-frigos" class="accordion-body collapse">
      <div class="accordion-inner">
				<p>
					<a href="#modal-ajout-{{$l->id}}" role="button" class="btn btn-primary" data-toggle="modal">Ajouter aux frigos</a>
					<a href="#modal-vols-{{$l->id}}" role="button" class="btn btn-warning" data-toggle="modal">Noter les vols</a>
					@if(isset($temps_ecoule[$l->id]))
						il y a {{$temps_ecoule[$l->id]}}
					@endif
				</p>
				<div class="modal modal-ajout" id="modal-ajout-{{$l->id}}" tabindex="-1" role="dialog" style="display:none;" aria-labelledby="modalAjoutLabel{{$l->id}}" aria-hidden="true">
				  <div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				    <h3 id="modalAjoutLabel{{$l->id}}">Remplir les  ({{$l->nom}})</h3>
				  </div>
				  <div class="modal-body">
						{{Former::inline_open()}}
							{{Former::select('produit_nom')->fromQuery(Produit::all(), 'nom', 'nom')}}
						  <input name="qte_volee" type="number" class="input-small" placeholder="Quantité">
							<input type="hidden" name="lieu_id" value="{{$l->id}}">
						  <button class="btn btn-primary">Ajouter</button>
						{{Former::close()}}
						{{Former::open('rf/frigos/add')}}
						<input type="hidden" name="lieu_id" value="{{$l->id}}">
					  <div class="modal-footer">
					    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
					    <button class="btn btn-primary">Remplir les frigos</button>
					  </div>
						{{Former::close()}}
				  </div>
				</div>
				<div class="modal modal-vols" id="modal-vols-{{$l->id}}" tabindex="-1" role="dialog" style="display:none;" aria-labelledby="modalLabel{{$l->id}}" aria-hidden="true">
				  <div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				    <h3 id="modalLabel{{$l->id}}">Gestion des vols ({{$l->nom}})</h3>
				  </div>
				  <div class="modal-body">
						{{Former::inline_open()}}
							{{Former::select('produit_nom')->fromQuery(Produit::all(), 'nom', 'nom')}}
						  <input name="qte_volee" type="number" class="input-small" placeholder="Quantité">
							<input type="hidden" name="lieu_id" value="{{$l->id}}">
						  <button class="btn btn-primary">Ajouter</button>
						{{Former::close()}}
						{{Former::open('rf/vols/add')}}
						<input type="hidden" name="lieu_id" value="{{$l->id}}">
					  <div class="modal-footer">
					    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
					    <button class="btn btn-primary">Noter les vols</button>
					  </div>
						{{Former::close()}}
				  </div>
				</div>
				<table class="table table-striped table-bordered">
				  <thead>
				    <tr>
				      <th>Groupe</th>
				      <th>Quantité dans les frigos</th>
				    </tr>
				  </thead>
				  <tbody>
				    @foreach(Stockgroupe::with('groupe')->where_lieu_id($l->id)->get() as $sg)
				      <tr>
								<td><a href="p/{{$sp->produit->id}}">{{$sg->groupe->nom}}</a></td>
								<td>{{$sg->qte_frigo}}</td>
				      </tr>
				    @endforeach
				  </tbody>
				</table>
      </div>
    </div>
  </div>
</div>
</div>
@endforeach
</div>
@endsection