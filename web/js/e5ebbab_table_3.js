/**
 * Contient toutes les données cochées de tous les tableaux de la page
 * Array(
 *   'tableId' => Array(
 *      'lineId' => Array(
 *          données
 *      )
 *    )
 * )
 * @type Array
 */
/**
 * Gestion des clics sur chaque checkbox
 */
window.addEvent('domready', function () {

    /**
     * Reset le tableau si l'utilisateur a utilisé la méthode "uncheck()" du TableauService
     * @see TableauService.php
     * @see head.html.twig
     */
    if(typeof phpk_reset_table !== 'undefined') {
        sessionStorage.setItem('phpk_table_data_'+phpk_reset_table, JSON.stringify({}));
    }

    $$('table.phpk_table').each(function (tableau) {
        tableau.getElements('tbody .phpk_decorator_checkbox input').each(function (line) {
            /**
             * Ajout de l'événement click sur chaque checkbox du tableau
             */
            line.addEvent('click', function() {
                recheck();
            });
        });
    });

    /**
     * Gestion des cases à cocher dans le cas d'un clic sur la checkbox de <th>
     */
    window.addEvent('phpk_table_check_all', function() {
        recheck();
    });

    function recheck() {
        $$('table.phpk_table').each(function (tableau) {
            /**
             * Pour chaque tableau, on créé un array
             */
            var tableId = tableau.get('id');

            tableau.getElements('tbody .phpk_decorator_checkbox input').each(function (line) {
                var checkbox = line.getParent('tr').getElement('td input[type=checkbox]');
                var lineId = checkbox.value;

                if(checkbox.checked) {
                    var columns = line.getParent('tr').getElements('td div');

                    /**
                     * Pour chaque ligne cochée dans le tableau, on créé un array
                     */
                    session = JSON.parse(sessionStorage.getItem('phpk_table_data_'+tableId));
                    if(!session) {
                        session = {};
                    }

                    if(typeof session[lineId] === "undefined") {
                        session[lineId] = [];

                        columns.each(function(data) {
                            if(data && data.get('html').toLowerCase().indexOf('<input') !== 0) {
                                /**
                                 * On ajoute chaque donnée dans le tableau de la ligne
                                 */
                                if(data.get('data-value')) {
                                    session[lineId].push(data.get('data-value'));
                                } else {
                                    session[lineId].push(data.get('html').trim());
                                }
                            }
                        });
                    }
                    sessionStorage.setItem('phpk_table_data_'+tableId, JSON.stringify(session));
                }
                else {
                    session = JSON.parse(sessionStorage.getItem('phpk_table_data_'+tableId));
                    if(session !== null && session[lineId] !== null) {
                        delete session[lineId];
                        sessionStorage.setItem('phpk_table_data_'+tableId, JSON.stringify(session));
                    }
                }
            });
        });
        /**
         * On lance l'évènement phpk_event_table_line_checked, notifiant le développeur d'un changement
         */
        window.fireEvent('phpk_event_table_line_checked');
    }

    recheck();
});
