<?php

  $modelFilter = "";
  if(isset($models))
      $modelFilter = implode(',',$models);

  if(!isset($limit))
      $limit = DEFAULT_RECENT_ACTIVITIES_LIMIT;

  $activityLogUri = "/audits/recent?models=$modelFilter&limit=$limit";

?>
<div class="recent-activities" data-src="<?php echo $activityLogUri; ?>">
	<h2>Recent activities<a class="cta refresh"></a></h2>
	<ul id="activity">
    </ul>
</div>
