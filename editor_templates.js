// Register a templates definition set named "default".
CKEDITOR.addTemplates( 'default',
{
	// The name of sub folder which hold the shortcut preview images of the templates.
	imagesPath : 'http://something.com/img/',

	// The templates definitions.
	templates :
		[
			{
				title: 'New Review',
				image: 'person.png',
				description: 'A new review is ready.',
				html:
					'<h2>New Review</h2>' +
					'<p>Please do your <strong>stuff</strong>.</p>'
			},
			{
				title: 'Release',
				html:
					'<h3>Release</h3>' +
					'<p>Type the text here.</p>'
			}
		]
});
