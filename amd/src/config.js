define([], function () {
    window.requirejs.config({
        paths: {
            "googlecharts": M.cfg.wwwroot + '/blocks/lemo4moodle/lib/googlecharts/loader',
            "plotly": M.cfg.wwwroot + '/blocks/lemo4moodle/lib/plotly/plotly-latest.min',
            "materialize": M.cfg.wwwroot + '/blocks/lemo4moodle/lib/materialize/materialize.min',
        },
        shim: {
            'googlecharts': {exports: 'googlecharts'},
            'plotly': {exports: 'plotly'},
            'materialize': {exports: 'materialize'},
        }
    });
});
