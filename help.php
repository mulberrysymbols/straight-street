<?
$_in_help_page=True;
include('_header.php');
?>

<div id="colmask">
	<div id="colmid">
		<div id="colright">
			<div id="col1wrap">
				<div id="col1pad">
					<div id="col1">
                        <div id="col1h">
                            <h1>Mulberry Symbol Set</h1>
                        </div>
                        <div id="col1b"
                        <!-- Column 1 start -->
                        <h2>Why is it free?</h2>
                        <p>We believe that communication is a fundamental human right. Many disabled people with impaired speech and other language difficulties require symbols to support their communication needs. This enables their desires, wishes and opinions to be expressed.</p> 
                        <p>So firstly, it is about equal participation. In school, for example, simple communication charts can allow students with such difficulties to fully engage in lessons.</p>
                        <p>Secondly, there are many talented people who can write computer programs, but who cannot contribute to this area when no free symbol set is available.</p>

                        <h2>How can I join in?</h2>
                        <p>The majority of people that register here simply wish to download our symbols.</p>
                        <p>But if you would like to be part of the team that comments on our symbols before they are published, you might consider becoming a Reviewer.</p>
                        <p>For more information on the was you can join in, check out the "type of login" below.</p>

                        <h2>Types of user login</h2>
                        <ul>
                          <li><a href="Helpfiles/typesoflogin.php#subscriber">- Subscriber</a></li>
                          <li><a href="Helpfiles/typesoflogin.php#reviewer">- Reviewer</a></li>
                          <li><a href="Helpfiles/typesoflogin.php#contributor">- Contributor</a></li>
                          <li><a href="Helpfiles/typesoflogin.php#developer">- Developer</a></li>
                        </ul>
                        <!-- Column 1 end -->
                        </div>
					</div>
				</div>
			</div>
			<div id="col2">
                <!-- Column 2 start -->
                <div id="col2h">
                <h1>News and events</h1>
                </div>
                <div id="col2b">
                <h2>Mulberry symbols - new version</h2>
                <div class='newsdate'>31 August 2011</div>
                <p>A new version of Mulberry symbols is released. Version 2.4 now includes over 2300 symbols.</p>

                <h2>Review 017 is completed</h2>
                <div class='newsdate'>31 August 2011</div>
                <p>Several hundred new symbols are soon to be published</p>

                <p><span id='more'>Also see</span> the <a href='news_archive.php'>news archive</a></p>
                </div>
                <!-- Column 2 end -->
    		</div>
			<div id="col3">
			<div id="col3h">
                <h1>Help information</h1>
            </div>
			<div id="col3b">
                <!-- Column 3 start -->
                <h2>User guides</h2>
                <p>These guides are in <a href='http://blog.kowalczyk.info/software/sumatrapdf/index.html'>pdf</a> format.</p>
                <ul>
<?
  function splitFilename($filename)
  {
      $pos = strrpos($filename, '.');
      if ($pos === false)
      { // dot is not found in the filename
          return array($filename, ''); // no extension
      }
      else
      {
          $basename = substr($filename, 0, $pos);
          $extension = substr($filename, $pos+1);
          return array($basename, $extension);
      }
  }
  foreach (glob("Helpfiles/*.pdf") as $file)
  {
    $fname = explode("/", $file);
    $name = splitFilename($fname[1]);
    $base= $name[0]; 
    print("<li><a href='$file'>- $base</a></li>");
  }

?>
                </ul>
                <h2>FAQ</h2>
                <p><a href="Helpfiles/FAQ.php">Frequently Asked Questions</a>.</p>
                
                <!-- Column 3 end -->
			</div>
			</div>
    	</div>
	</div>
</div>


<div id="colmask2">
	<div id="colmid">
		<div id="colright">
			<div id="col1wrap">
				<div id="col1pad">  
					<div id="col1">
						<!-- Column 1 start -->
                        <div id='splitl'>
                            <div id="col1h2">
                            <h1>About Us</h1>
                            </div>
                            <div id="col1b2">
                            <h2>History</h2>
                                <p><a href='Helpfiles/about.php#about'>The story of Straight Street</a></p>
                            <h2>Our Funding</h2>
                                <p><a href='Helpfiles/about.php#funding'>How we get funding</a></p>
                            </div>
                        </div>
                        <div id='splitr'>
                            <div id="col1ah2">
                            <h1>Future</h1>
                            </div>
                            <div id="col1ab2">
                            <h2>Photos and sounds</h2>
                                <p>Photos and sounds will be coming to Straight Street...<br/><a href='Helpfiles/future.php#newmedia'>Read more</a></p>
                            </div>
                        </div>
						<!-- Column 1 end -->
					</div>
				</div>
			</div>
			<div id="col2">
    			<div id="col2h2">
                    <h1>Get a list of symbols</h1>
                </div>
                <div id="col2b2"
                <!-- Column 2 start -->
                    <p><a href="Helpfiles/OtherDocs/SymbolList.pdf"><img title="Get list of symbols" alt="Get list of symbols" src="/img/workarea/j_media1.jpg"/></a></p>
                <!-- Column 2 end -->
        		</div>
    		</div>
			<div id="col3">
			<div id="col3h2">
                <h1>Technical</h1>
            </div>
			<div id="col3b2">
                <!-- Column 3 start -->
                <h2>Symbol formats</h2>
                    <p><a href='Helpfiles/technical.php#formats'>Provided in "wmf" and "svg"</a></p>
                    <p> soon to be provided in .png format !!! </p>
                <h2>Open source</h2>
                    <p><a href='Helpfiles/technical.php#opensource'>Free, reusable &amp; sharable</a></p>
                    <p> see <a href='api'>API</a> for public access to symbols.</p>
                <h2>Service &amp; support</h2>
                    <p><a href='Helpfiles/technical.php#support'>Questions or other needs</a</p>
                <!-- Column 3 end -->
	    	</div>
			</div>
    	</div>
	</div>
</div>

</div>

<?
//include('_footer.php');
?>
