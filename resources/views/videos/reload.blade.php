@php
use \App\Providers\ThemaProvider
@endphp
@foreach ($videos as $i => $video)
<div class="col-xl-6 col-lg-6 col-sm-12 col-xs-12">
    <div class="card">
        <div class="row g-0 align-items-center">
            <div class="col-md-4">
                <img class="card-img img-fluid" src="storage/videos/<?php print $video["thumbmail"]; ?>" alt="Card image">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title"><?php print $video["title"]; ?></h5>
                    <p class="card-text"><?php print str_replace(["<div>", "</div>"], ["", ""], strlen($video["description"]) > 250 ? Str::limit($video["description"], 200 - 3) : $video["description"]); ?></p>
                    <p class="card-text"><button class="btn btn-primary" type="button"  data-bs-toggle='modal' data-bs-target='.bs-video-modal-xl' data-title="<?php print $video["title"]; ?>" data-path="<?php print App\Providers\Utils::extractID($video["youtube"]) ?>"><i class="fa fa-eye"></i> Assistir Tutorial</button></p>
                    <p class="card-text"><small class="text-muted">Atualizado <?php print date("d/m/y", strtotime($video["updated_at"])); ?></small></p>
                </div>
            </div>
        </div>
    </div>
</div><!-- end col -->
@endforeach
{{ $videos->links('pagination::bootstrap-4') }}
