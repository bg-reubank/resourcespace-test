<?php 
if (!hook("renderresultthumb")) 
	{ ?>
	<!--Resource Panel-->
	<div class="ResourcePanelShell" id="ResourceShell<?php echo htmlspecialchars($ref)?>">
		<div class="ResourcePanel <?php hook('thumbsviewpanelstyle'); ?> ResourceType<?php echo $result[$n]['resource_type']; ?>">
		<?php  
		if ($resource_type_icons) 
			{ ?>
	 		<div class="ResourceTypeIcon IconResourceType<?php echo $result[$n]["resource_type"];  ?>"></div>
			<?php 
			}
		hook ("resourcethumbtop");
		if (!hook("renderimagethumb")) 
			{
			# Work out image to use.
			if(isset($watermark))
				{
				$use_watermark=check_use_watermark();
				}
			else
				{
				$use_watermark=false;	
				}
			$thm_url=get_resource_path($ref,false,($retina_mode?"pre":"thm"),false,$result[$n]["preview_extension"],-1,1,$use_watermark,$result[$n]["file_modified"]);
			if (isset($result[$n]["thm_url"])) {$thm_url=$result[$n]["thm_url"];} # Option to override thumbnail image in results, e.g. by plugin using process_Search_results hook above
			?>
			<table border="0" class="ResourceAlign icon_type_<?php echo $result[$n]["resource_type"]; ?> icon_extension_<?php echo $result[$n]['file_extension']; ?><?php if(!hook("replaceresourcetypeicon")){ if (in_array($result[$n]["resource_type"],$videotypes)) { ?> IconVideo<?php } ?><?php } hook('searchdecorateresourcetableclass'); ?>">
			<?php hook("resourcetop")?>
			<tr>
			<td>
				<!-- new code start -->
				<?php
				$show_flv=false;
				$use_mp3_player=false;
				if((in_array($result[$n]["file_extension"],$ffmpeg_supported_extensions) || $result[$n]["file_extension"]=="flv") && $video_player_thumbs_view){ 
					$flvfile=get_resource_path($ref,true,"pre",false,$ffmpeg_preview_extension);
					if (!file_exists($flvfile)){
						$flvfile=get_resource_path($ref,true,"",false,$ffmpeg_preview_extension);
					}
					elseif(!(isset($result[$n]['is_transcoding']) && $result[$n]['is_transcoding']!=0) && file_exists($flvfile) && (strpos(strtolower($flvfile),".".$ffmpeg_preview_extension)!==false)){
						$show_flv=true;
					}
				}
				else
					{
					// Set $use_mp3_player switch if appropriate
					$use_mp3_player = ($mp3_player_thumbs_view && !(isset($result[$n]['is_transcoding']) && $result[$n]['is_transcoding']==1) && ((in_array($result[$n]["file_extension"],$ffmpeg_audio_extensions) || $result[$n]["file_extension"]=="mp3") && $mp3_player));
					if ($use_mp3_player)
						{
						$mp3realpath=get_resource_path($ref,true,"",false,"mp3");
						if (file_exists($mp3realpath))
							{$mp3path=get_resource_path($ref,false,"",false,"mp3");}
						}
					}
				if(isset($flvfile) && hook("replacevideoplayerlogic","",array($flvfile,$result,$n))){
				
				}
    			elseif($show_flv){
					# Include the Flash player if an FLV file exists for this resource.
					if(!hook("customflvplay")){
						include "video_player.php";
					}
				}
				elseif ($use_mp3_player && file_exists($mp3realpath) && !hook("custommp3player"))
					{
					$thumb_path=get_resource_path($ref,true,"pre",false,"jpg");
					if(file_exists($thumb_path))
						{$thumb_url=get_resource_path($ref,false,"pre",false,"jpg"); }
					else
						{$thumb_url=$baseurl_short . "gfx/" . get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],false);}

					include "mp3_play.php";
					}
				else{?><!-- new code end -->
				<div id="triangle" style="border-color: transparent transparent rgb(7, 101, 145); border-width: 0px 0px 200px 200px;"></div>
				<a 
					style="position:relative;" 
					href="<?php echo $url?>"  
					onClick="return <?php echo ($resource_view_modal?"Modal":"CentralSpace") ?>Load(this,true);" 
					title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field])))?>"
				>
                        <?php 
                        if(1 == $result[$n]['has_image'])
                        {
                        ?>
                        <img 
                        <?php
                        if('' != $result[$n]['thumb_width'] && 0 != $result[$n]['thumb_width'] && '' != $result[$n]['thumb_height'])
                            {
                            ?>
                            width="<?php echo $result[$n]["thumb_width"]?>" 
                            height="<?php echo $result[$n]["thumb_height"]?>" 
                            <?php
                            }
                            ?>
                        src="<?php echo $thm_url ?>" 
                        class="ImageBorder" 
                        alt="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($result[$n]["field".$view_title_field]))); ?>"
                        />
                        <?php
                        // For videos ($ffmpeg_supported_extensions), if we have snapshots set, add code to fetch them from the server
                        // when user hovers over the preview thumbnail
                        /*
                        [5] => Array
                            (
                                [ref] => 6
                                [resource_type] => 3
                                [has_image] => 1
                                [is_transcoding] => 0
                                [hit_count] => 14
                                [creation_date] => 2016-05-23 13:25:01
                                [rating] => 
                                [user_rating] => 
                                [user_rating_count] => 
                                [user_rating_total] => 
                                [file_extension] => mp4
                                [preview_extension] => jpg
                                [image_red] => 0
                                [image_green] => 0
                                [image_blue] => 0
                                [thumb_width] => 150
                                [thumb_height] => 84
                                [archive] => 0
                                [access] => 0
                                [colour_key] => W
                                [created_by] => 1
                                [file_modified] => 2016-07-28 13:32:19
                                [file_checksum] => 
                                [request_count] => 0
                                [new_hit_count] => 24
                                [expiry_notification_sent] => 0
                                [preview_tweaks] => 0|1
                                [file_path] => 
                                [group_access] => 
                                [user_access] => 
                                [field12] => 2016-05-23 13:24
                                [field8] => Test Video
                                [field3] => 
                                [score] => 14
                            )
                        */
                        if(1 < $ffmpeg_snapshot_frames && in_array($result[$n]['file_extension'], $ffmpeg_supported_extensions) && 0 < get_video_snapshots($ref))
                            {
                            ?>
                            <script>
                            // add in a range slider on the UI with range as the number of snapshots found
                            alert('TODO: add in a range slider');
                            </script>
                            <?php
                            }
                        } 
					else 
						{ ?>
						<img 
							border=0 
							src="<?php echo $baseurl_short?>gfx/<?php echo get_nopreview_icon($result[$n]["resource_type"],$result[$n]["file_extension"],false) ?>" 

						/>
						<?php 
						}
					hook("aftersearchimg","",array($result[$n]))?>
				</a>
				<!-- new code start -->
				<?php } ?>
				<!-- new code end -->
			</td>
			</tr>
			</table>
			<?php 
			} ?> 
		<!-- END HOOK Renderimagethumb-->
		<?php 
		hook("beforesearchstars");
		if ($display_user_rating_stars && ($k=="" || $internal_share_access))
			{ 
			if (!hook("replacesearchstars"))
				{
				if ($result[$n]['user_rating']=="") {$result[$n]['user_rating']=0;}
				$modified_user_rating=hook("modifyuserrating");
				if ($modified_user_rating!=''){$result[$n]['user_rating']=$modified_user_rating;}
				?>
				<div  class="RatingStars" onMouseOut="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $result[$n]['user_rating']?>,'StarCurrent');">&nbsp;<?php 
				for ($z=1;$z<=5;$z++)
					{
					?>
					<a 
						href="#" 
						onMouseOver="UserRatingDisplay(<?php echo $result[$n]['ref']?>,<?php echo $z?>,'StarSelect');" 
						onClick="UserRatingSet(<?php echo $userref?>,<?php echo $result[$n]['ref']?>,<?php echo $z?>);return false;" 
						id="RatingStarLink<?php echo $result[$n]['ref'].'-'.$z?>"
					>
						<span 
							id="RatingStar<?php echo $result[$n]['ref'].'-'.$z?>" 
							class="Star<?php echo ($z<=$result[$n]['user_rating']?"Current":"Empty")?>"
						>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</span>
					</a>
					<?php
					}
				?>
				</div>
				<?php 
				} // end hook replacesearchstars
			}
		if (!hook("replaceicons")) 
			{
			hook("icons");
			} //end hook replaceicons
		if (!hook("rendertitlethumb")) {} ?> <!-- END HOOK Rendertitlethumb -->			
		<?php
		$df_alt=hook("displayfieldsalt");
		$df_normal=$df;
		if ($df_alt){$df=$df_alt;}
		# thumbs_display_fields
		for ($x=0;$x<count($df);$x++)
			{
			if(!in_array($df[$x]['ref'],$thumbs_display_fields))
				{continue;}
			
			#value filter plugin -tbd	
			$value=@$result[$n]['field'.$df[$x]['ref']];
			$plugin="../plugins/value_filter_" . $df[$x]['name'] . ".php";
			if ($df[$x]['value_filter']!="")
				{eval($df[$x]['value_filter']);}
			else if (file_exists($plugin)) 
				{include $plugin;}

			# swap title fields if necessary
			if (isset($metadata_template_resource_type) && isset ($metadata_template_title_field))
				{
				if (($df[$x]['ref']==$view_title_field) && ($result[$n]['resource_type']==$metadata_template_resource_type))
					{
					$value=$result[$n]['field'.$metadata_template_title_field];
					}
				}

			// extended css behavior 
			if (in_array($df[$x]['ref'],$thumbs_display_extended_fields) &&
			((isset($metadata_template_title_field) && $df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field)))
				{
				if (!hook("replaceresourcepanelinfo"))
					{ ?>
					<div class="ResourcePanelInfo ResourceTypeField<?php echo $df[$x]['ref']?>">
						<div class="extended">
						<?php 
						if ($x==0)
							{ // add link if necessary ?>
							<a 
								href="<?php echo $url?>"  
								onClick="return <?php echo ($resource_view_modal?"Modal":"CentralSpace") ?>Load(this,true);" 
								title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"
							>
							<?php 
							} //end link
						echo format_display_field($value);
						if ($show_extension_in_search) 
							{ 
							echo " " . str_replace_formatted_placeholder("%extension", $result[$n]["file_extension"], $lang["fileextension-inside-brackets"]);
							}
						if ($x==0)
							{ // add link if necessary ?>
							</a>
							<?php 
							} //end link?> 
						&nbsp;
						</div>
					</div>
					<?php 
					} /* end hook replaceresourcepanelinfo */ ?>
				<?php 
				// normal behavior
				} 
			else if  ((isset($metadata_template_title_field)&&$df[$x]['ref']!=$metadata_template_title_field) || !isset($metadata_template_title_field) ) 
				{
				if (!hook("replaceresourcepanelinfonormal"))
					{ ?>
					<div class="ResourcePanelInfo  ResourceTypeField<?php echo $df[$x]['ref']?>">
						<?php 
						if ($x==0)
							{ // add link if necessary ?>
							<a 
								href="<?php echo $url?>"  
								onClick="return <?php echo ($resource_view_modal?"Modal":"CentralSpace") ?>Load(this,true);" 
								title="<?php echo str_replace(array("\"","'"),"",htmlspecialchars(i18n_get_translated($value)))?>"

							>
							<?php 
							} //end link
						echo highlightkeywords(tidy_trim(TidyList(i18n_get_translated($value)),$search_results_title_trim),$search,$df[$x]['partial_index'],$df[$x]['name'],$df[$x]['indexed']);
						if ($x==0)
							{ // add link if necessary ?>
							</a>
							<?php 
							} //end link ?>
						&nbsp;
					</div>
					<div class="clearer"></div>
					<?php 
					}
				} /* end hook replaceresourcepanelinfonormal */
				hook("processthumbsfields");
			}
		$df=$df_normal;
		?>
		<!-- Checkboxes -->
		<div class="ResourcePanelIcons">
			<?php
			if(!hook("thumbscheckboxes"))
			{
			if ($use_checkboxes_for_selection)
				{
				if(!in_array($result[$n]['resource_type'],$collection_block_restypes))	
					{?>
					<input 
						type="checkbox" 
						id="check<?php echo htmlspecialchars($ref)?>" 
						class="checkselect" 
						<?php 
						if (in_array($ref,$collectionresources))
							{ ?>
							checked
							<?php 
							} ?> 
						onclick="if (jQuery('#check<?php echo htmlspecialchars($ref)?>').attr('checked')=='checked'){ AddResourceToCollection(event,<?php echo htmlspecialchars($ref)?>); } else if (jQuery('#check<?php echo htmlspecialchars($ref)?>').attr('checked')!='checked'){ RemoveResourceFromCollection(event,<?php echo htmlspecialchars($ref)?>); }"
					>
					&nbsp;
					<?php 
					}
				else
					{
					?>
					<input type="checkbox" style="opacity: 0;">
					<?php
					}
				}
			} # end hook thumbscheckboxes
		if(!hook("replacethumbsidinthumbnail"))
			{
			if ($display_resource_id_in_thumbnail && $ref>0) 
				{ echo htmlspecialchars($ref); } 
			else 
				{ ?>&nbsp;<?php }
			} # end hook("replacethumbsidinthumbnail")

		if (!hook("replaceresourcetools"))
			{ 
			include "resource_tools.php";
			} // end hook replaceresourcetools ?>
        </div>	
	</div>
	
	</div>
	<?php 
	} # end hook renderresultthumb


