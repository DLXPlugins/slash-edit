module.exports = function( grunt ) {
	grunt.initConfig( {
		compress: {
			main: {
				options: {
					archive: 'slash-edit.zip',
				},
				files: [
					{ src: [ 'slash-edit.php' ], dest: '/', filter: 'isFile' },
					{ src: [ 'index.php' ], dest: '/', filter: 'isFile' },
					{ src: [ 'readme.txt' ], dest: '/', filter: 'isFile' },
					{ src: [ 'LICENSE' ], dest: '/', filter: 'isFile' },
				],
			},
		},
	} );
	grunt.registerTask( 'default', [ 'compress' ] );

	grunt.loadNpmTasks( 'grunt-contrib-compress' );
};
