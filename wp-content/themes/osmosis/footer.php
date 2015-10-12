
			<?php grve_print_bottom_bar(); ?>
			<?php $grve_sticky_footer = grve_visibility( 'sticky_footer' ) ? 'yes' : 'no'; ?>

			<footer id="grve-footer" data-sticky-footer="<?php echo esc_attr( $grve_sticky_footer ); ?>">
<!--
                <div class="audio3_html5_bottom_div">
                    <div class="audio3_html5">
                    <audio id="audio3_html5_white" preload="metadata">
                      <div class="xaudioplaylist">
                          
                          <ul>
                              <li class="xtitle">Aquatic Plaza</li>
                              <li class="xauthor">Convenient Integrative Mood</li>
                              <li class="xsources_mp3">../../../audio/cim-aquaticplaza.mp3</li>
                          </ul> 
                           <ul>
                              <li class="xtitle">The Lounge</li>
                              <li class="xauthor">Bensound</li>
                              <li class="xsources_mp3">../../../audio/bensound-thelounge.mp3</li>
                          </ul>                 
                      
                          <ul>
                              <li class="xtitle">India</li>
                              <li class="xauthor">Bensound</li>
                              <li class="xsources_mp3">../../../audio/bensound-india.mp3</li>
                          </ul>    
                                          
                      
                          <ul>
                              <li class="xtitle">Sailin</li>
                              <li class="xauthor">Ghostling</li>
                              <li class="xsources_mp3">../../../audio/ghostling-sailin.mp3</li>
                          </ul>    
                          <ul>
                              <li class="xtitle">Days Of Being Wild</li>
                              <li class="xauthor">Hong Kong Express</li>
                              <li class="xsources_mp3">../../../audio/hkexpress-daysofbeingwild.mp3</li>
                          </ul>                 
                      
                          <ul>
                              <li class="xtitle">Saw You Last Night</li>
                              <li class="xauthor">Madison Avenue</li>
                              <li class="xsources_mp3">../../../audio/madisonavenue-sawyoulastnight.mp3</li>
                          </ul>    
                          <ul>
                              <li class="xtitle">Winter Sadness</li>
                              <li class="xauthor">The Music Of the Now Age</li>
                              <li class="xsources_mp3">../../../audio/themusicofthenowage-wintersadness.mp3</li>
                          </ul>                 
                      
                                                                                        
                      </div>
                  No HTML5 audio playback capabilities for this browser. Use <a href="https://www.google.com/intl/en/chrome/browser/">Chrome Browser!</a>    
                    </audio>     
                    </div>             
	           </div>
-->
				<div class="grve-container">

				<?php grve_print_footer_widgets(); ?>
				<?php grve_print_footer_bar(); ?>

				</div>
				<?php grve_print_title_bg_image_container( 'footer_background' ); ?>
			</footer>

		</div> <!-- end #grve-theme-wrapper -->

		<?php wp_footer(); // js scripts are inserted using this function ?>

	</body>

</html>