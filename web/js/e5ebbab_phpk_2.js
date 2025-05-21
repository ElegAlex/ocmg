window.addEvent('load', function (event) {
    phpk_load($$('body'));
});

function phpk_load(parent) {

    if ($type(parent) === 'array') {
        parent = parent[0];
    }

    /**
     * Gestion de l'aide (twig: phpk('aide'))
     */
    parent.getElements('a.modale_aide').each(function (element) {
        element.addEvent('click', function () {
            $('phpk_modale_aide_contenu').set('html', '<p>' + element.getProperty('data-aide') + '</p>');
            $$('#phpk_modale_aide .fenetre-title').set('html', element.getProperty('data-title'));
            phpk_modale_aide.open();
            phpk_load($('phpk_modale_aide'));
        });
    });

    /**
     * Gestion des modales Ajax pour tout ce qui a comme classe CSS .modale_ajax
     */
    // Stockage du message d'attente de base puisqu'on le modifie
    var ajaxMessageAttente = $('phpk_modale_ajax_contenu').get('html');
    // Stockage du style de base de la fenêtre modale puisqu'on y apporte des modifs
    var ajaxWindowStyle = $('phpk_modale_ajax').get('style');

    parent.getElements('a.modale_ajax').each(function (element) {
        element.addEvent('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var request = new Request.HTML({
                method: 'get',
                evalScripts: true,
                evalResponse: true,
                url: element.getProperty('data-url'),
                update: $('phpk_modale_ajax_contenu'),
                onRequest: function () {
                    $('phpk_modale_ajax_contenu').set('html', ajaxMessageAttente);
                    $('phpk_modale_ajax').set('style', ajaxWindowStyle);
                    $$('#phpk_modale_ajax .fenetre-title').set('html', element.getProperty('data-title'));
                    phpk_modale_ajax.open();
                },
                onComplete: function (response) {
                    $('phpk_modale_ajax').setStyles({width: 'inherit', height: 'inherit'});
                    // On adapte la fenêtre à son contenu et on la centre
                    var modaleSize = $('phpk_modale_ajax').getSize();
                    var windowSize = window.getSize();
                    var left = ((windowSize.x - modaleSize.x) / 2);
                    var top = ((windowSize.y - modaleSize.y) / 2);
                    $('phpk_modale_ajax').setStyles({top: top, left: left});
                    phpk_load($('phpk_modale_ajax'));
                },
                onFailure: function (response) {
                    $('phpk_modale_ajax_contenu').set('html', '<p>Une erreur est survenue.</p>');
                    var modaleSize = $('phpk_modale_ajax').getSize();
                    var windowSize = window.getSize();
                    var left = ((windowSize.x - modaleSize.x) / 2);
                    var top = ((windowSize.y - modaleSize.y) / 2);
                    $('phpk_modale_ajax').setStyles({width: 'inherit', height: 'inherit', top: top, left: left});
                }
            }).send();

        });
    });

    /**
     * Gestion du prompt (bouton predefined: question)
     */
    parent.getElements('a.prompt').each(function (element) {
        element.addEvent('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('phpk_modale_prompt_contenu').getParent('.fenetre').getChildren('.fenetre-header .fenetre-title').
                    set('html', element.getProperty('data-question-title'));

            if (element.getProperty('data-question-type') === 'textarea') {
                $('phpk_modale_prompt_contenu').set('html',
                        '<p>' + element.getProperty('data-question') + '</p>' +
                        '<textarea id="phpk_prompt_input" name="reponse" cols="50" rows="5">' + element.getProperty('data-question-value') + '</textarea>'
                        );
            } else {
                $('phpk_modale_prompt_contenu').set('html',
                        '<p>' + element.getProperty('data-question') + '</p>' +
                        '<input type="text" id="phpk_prompt_input" name="reponse" value="' + element.getProperty('data-question-value') + '" class="center" />'
                        );
            }

            /**
             * Gestion de l'appel callback
             * javascript: callback(reponse);
             */
            var callCallback = function () {
                window[element.getProperty('data-question-callback')]($("phpk_prompt_input").value);
                phpk_modale_prompt.close();
            };
            if (element.getFirst('span').hasClass('btn_action')) {
                $('phpk_prompt_button').removeEvents('click', callCallback);
                if (element.getProperty('data-question-callback') !== '') {
                    $('phpk_prompt_button').addEvent('click', callCallback);
                }
                phpk_modale_prompt.open();
                $('phpk_prompt_input').focus();
            }
        });
    });

    /**
     * Gestion de la fenêtre de confirmation (twig: button.a({ confirm: true, confirmText: 'Texte' }))
     */
    parent.getElements('a.confirm').each(function (element) {
        element.addEvent('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('phpk_modale_confirm_contenu').set('html', '<span>' + element.getProperty('texte') + '</span>');
            var callCallback = function () {
                if (element.get('data-confirm-callback')) {
                    window[element.get('data-confirm-callback')]();
                } else {
                    window.location.href = element.get('href');
                }
                phpk_modale_confirm.close();
            };
            if (element.getFirst('span').hasClass('btn_action')) {
                $('phpk_confirm_button').removeEvents('click', callCallback);
                $('phpk_confirm_button').addEvent('click', callCallback);
                phpk_modale_confirm.open();
            }
        });
    });

    /**
     * Gestion des notifications
     */
    parent.getElements('.phpk-flash-fade').each(function (element) {
        setTimeout(function () {
            element.fade('out');
        }, 10000);
        element.addEvent('mouseover', function () {
            element.fade('out');
        });
    });

    /*
     * Gestion des select
     */
    parent.getElements('div.zone_combo>select:not([multiple])').each(function (element) {
        var width = element.get('data-width') || null;
        var disable_search_threshold = (element.get('data-search') && element.get('data-search') == "always") ? 0 : 1000;
        var placeholder = element.get('data-placeholder') || "";
        var no_results_text = element.get('data-no-results-text') || "";
        new Chosen(element, {
            width: width,
            disable_search_threshold: disable_search_threshold,
            placeholder: placeholder,
            no_results_text: no_results_text
        });
    });

    parent.getElements('div.zone_combo>select[multiple]').each(function (element) {
        var width = element.get('data-width') || null;
        var disable_search_threshold = (element.get('data-search') && element.get('data-search') === "always") ? 0 : 1000;
        var placeholder = element.get('data-placeholder') || "";
        var no_results_text = element.get('data-no-results-text') || "test";

        /*
         * Lorsque l'attribut data-url est renseigné sur le select alors un champ Chosen.Ajax est créé
         *
         * data-url : indique l'url à utiliser pour la requête Ajax
         * data-query : indique le nom du paramètre à utiliser dans la requête Ajax
         *
         * [data-url]?[data-query]=valeur
         *
         */
        if (element.get('data-url') && element.get('data-query')) {
            var chosen = new Chosen.Ajax(element, {
                url: element.get('data-url'),
                query: element.get('data-query'),
                width: width,
                disable_search_threshold: disable_search_threshold,
                placeholder: placeholder,
                no_results_text: no_results_text
            });
        } else {
            var chosen = new Chosen(element, {
                width: width,
                disable_search_threshold: disable_search_threshold,
                placeholder: placeholder,
                no_results_text: no_results_text,
            });
        }

        element.addEvent('change', function (event, params) {
            var selected = [];
            chosen.results_data.each(function (option) {
                if (option.selected) {
                    selected.push(option.text);
                }
            });
            $$('#' + element.id + ' option').each(function (option) {
                if (selected.contains(option.get('text'))) {
                    option.selected = true;
                    option.set('selected', 'selected');
                }
            });
        });

    });


    /**
     * TABLEAUX
     */

    /**
     * Gestion de la checkbox de sélection pour les tableaux (Decorator::CHECKBOX)
     * Au clic, sélectionne/désélectionne l'ensemble des checkbox de la page
     */
    parent.getElements('input.phpk_check_table').each(function (element) {
        element.addEvent('click', function () {
            window.fireEvent('phpk_table_check_all', element);
        });
    });
    window.addEvent('phpk_table_check_all', function (element) {
        if (element) {
            element.getParent('table').getElements('input[type=checkbox]').each(function (checkbox) {
                checkbox.set('checked', element.get('checked'));
            });
            window.fireEvent('phpk_table_check_all');
        }
    });


    parent.getElements('.phpk_btn_restore').each(function (element) {
        element.addEvent('click', function () {
            var table = element.getParent('table');
            table.getElements('tr.filtre input[type=text],tr.filtre input[type=hidden]').each(function (filtre) {
                filtre.set('value', '');
            });
            table.getElements('tr.filtre select').each(function (filtre) {
                filtre.selectedIndex = '';
            });
        });
    });

    /**
     * Gestion du tri
     */

    // Prise en compte keypress sur le CTRL
    var ctrlDown = false;
    var ctrTables = [];

    $(document).addEvent('keydown:keys(control)', function (event) {
        ctrlDown = true;
    });

    $(document).addEvent('keyup:keys(control)', function (event) {
        ctrlDown = false;

        ctrTables.each(function (table) {

            var tableSortCols = "";
            var tableSortSens = "";

            // On tri le tableau par ordre de selection
            var colonnes = $(table).getElements('th>a.phpk_tri[data-ordre]').sort(function (col1, col2) {
                return col1.get('data-ordre') - col2.get('data-ordre');
            });

            colonnes.each(function (colonne) {
                var icone = colonne.getParent();

                switch (true) {
                    case icone.hasClass('tri-asc'):
                    case icone.hasClass('tri-desc'):
                        tableSortCols += colonne.get('data-colonne') + ",";
                        tableSortSens += colonne.get('data-sens') + ",";
                        break;
                    default:
                        break;
                }
            });

            tableSortCols = tableSortCols.substring(0, tableSortCols.length - 1);
            tableSortSens = tableSortSens.substring(0, tableSortSens.length - 1);

            $('phpk_table[' + table + '][tri][colonne]').set('value', tableSortCols);
            $('phpk_table[' + table + '][tri][sens]').set('value', tableSortSens);

            $(table).getParent('form').submit();

        });
    });

    // Traitement du click sur une colonne
    parent.getElements('.phpk_tri').each(function (element) {
        element.addEvent('click', function (event) {

            var tableId = element.getParent('table').get('id');

            if (ctrlDown) {

                event.stopPropagation();
                event.preventDefault();

                var icone = element.getParent();
                var sens = {ancien: {data: element.get('data-sens'), tri: ''}, nouveau: {data: '', tri: ''}};
                var nbreTri = $(tableId).get('data-nbre-tri') === null ? 0 : parseInt($(tableId).get('data-nbre-tri'));

                switch (true) {
                    case icone.hasClass('tri-asc'):
                        sens.nouveau.data = 'SORT_DESC';
                        sens.nouveau.tri = 'tri-desc';
                        break;
                    case icone.hasClass('tri-desc'):
                        sens.nouveau.data = '';
                        sens.nouveau.tri = 'tri';
                        break;
                    default:
                    case icone.hasClass('tri'):
                        sens.nouveau.data = 'SORT_ASC';
                        sens.nouveau.tri = 'tri-asc';
                        break;
                }

                element.set('data-sens', sens.nouveau.data);
                icone.removeClass('tri tri-asc tri-desc').addClass(sens.nouveau.tri);

                if (element.get('data-ordre') === null) {
                    var ordre = nbreTri + 1;
                    element.set('data-ordre', ordre);
                    $(tableId).set('data-nbre-tri', ordre);
                }

                if (!ctrTables.contains(tableId)) {
                    ctrTables.push(tableId);
                }

            } else {
                $('phpk_table[' + tableId + '][tri][colonne]').set('value', element.get('data-colonne'));
                $('phpk_table[' + tableId + '][tri][sens]').set('value', element.get('data-sens'));
                element.getParent('form').submit();
            }
        });
    });

    /**
     * Gestion de la pagination
     */
    parent.getElements('.phpk_page').each(function (element) {
        element.addEvent('click', function () {
            var tableId = element.getParent('form').getElement('table.phpk_table').get('id');
            $('phpk_table[' + tableId + '][page]').set('value', element.get('data-page'));

            element.getParent('form').submit();
        });
    });

    /**
     * Gestion du changement de nombre d'éléments par page
     */
    parent.getElements('.phpk_pageSize').each(function (element) {
        element.addEvent('change', function () {
            element.getParent('form').submit();
        });
    });

    /*
     * Gestion des boutons de fermeture de modales
     */
    parent.getElements('.phpk_close').each(function (element) {
        element.addEvent('click', function () {
            var modale = element.getParent(".modale");
            if (modale) {
                var o = eval(modale.get('id'));
                o.close();
            }
        });
    });

    parent.getElements('a.confirm[disabled="disabled"]', 'a.prompt[disabled="disabled"]').each(function (element) {
        element.removeEvents('click');
    });

    Infobulle.init(parent.getElements('.infobulle'));

    /*
     * Gestion de l'attribut disableOnSubmit des boutons permettant d'activer leur désactivation
     * à la soumission du formulaire auquel ils sont rattachés.
     * Cela permet d'éviter la double soumission d'un formulaire.
     */
    parent.getElements('form').each(function (formulaire) {
        formulaire.getElements('button[data-disable-on-submit=true]').each(function (bouton) {
            bouton.addEvent('click', function() {
                bouton.setAttribute('data-clicked', true);
            });
        });
        formulaire.addEvent('submit', function() {
            formulaire.getElements('button[data-disable-on-submit=true]').each(function (bouton) {
                bouton.setAttribute('disabled', 'disabled');
                /**
                 * Le fait de rendre un élément disabled fait qu'il n'est pas soumis au formulaire.
                 * Ainsi, si le développeur a mis un nom au bouton et une valeur, rien n'est passé au formulaire.
                 * En ajoutant ce champ hidden, on passe explicitement les informations à la soumission.
                 */
                if(bouton.getAttribute('data-clicked')) {
                    formulaire.appendHTML('<input type="hidden" name="'+bouton.getAttribute('name')+'" value="'+bouton.getAttribute('value')+'" />');
                }
            });
       });
    });

}

/*
 * Création d'un accordeon à partir d'un element passé en paramètre
 */
function phpk_accordeon(accordeon) {
    var elements = $(accordeon).getChildren('div.bpliable');
    if (elements) {
        elements.each(function (element) {
            var content = $(element.get('aria-content'));
            var ajax = element.get('aria-ajax') == "true";
            var href = element.get('aria-href');
            var body = element.getChildren('div.bpliable-body')[0];

            if (ajax) {
                BlocPliableTag.initAjax(element, href, false);
            } else {
                if (body) {
                    body.set('html', content.get('html'));
                }
                BlocPliableTag.initLocal(element);
                content.destroy();
            }

        });

    }
}

/*
 * Création d'un menu à onglets
 */

function phpk_onglets(onglet) {

    var elements = $(onglet).getChildren('li>a');
    var selection = null;

    if (elements) {
        elements.each(function (element, i) {

            var contenu = $(element.get('aria-content'));
            var conteneur = $(element.get('aria-controls'));
            var selected = element.get('aria-selected') === "true";
            var ajax = element.get('aria-ajax') === "true";

            // On vérifie si le contenu existe
            if (contenu !== null) {
                // On cache l'element
                contenu.hide();
                // On le déplace dans le conteneur
                contenu.inject(conteneur);
            }

            // Si c'est de l'ajax, on crée un conteneur
            if (ajax) {
                var conteneurName = element.get('aria-controls') + '_ajax_' + i;
                var conteneurAjax = new Element('div', {id: conteneurName});

                conteneurAjax.hide();
                conteneurAjax.inject(conteneur);

                element.set('aria-ajax-container', conteneurName);
            }

            // On ajoute un evenment click sur l'onglet
            element.addEvent('click', function (event) {

                var contenu = $(this.get('aria-content'));
                var selected = this.get('aria-selected') === "true";
                var disabled = this.get('aria-disabled');
                var ajax = this.get('aria-ajax') === "true";
                var href = this.get('href');

                if (contenu || ajax) {

                    // On stoppe la propagation de l'evenement
                    if (event) {
                        event.stopPropagation();
                        event.preventDefault();
                    }

                    // Si celui-ci est désactivé ou selectionné alors on ne fait rien
                    if ((!disabled && !selected) || !event) {

                        var conteneur = $(this.get('aria-controls'));

                        conteneur.set('aria-busy', true);

                        // On cache les autres contenus
                        conteneur.getChildren('div').hide();

                        if (ajax) {
                            var href = $(element.get('href'));
                            var conteneurAjax = $(element.get('aria-ajax-container'));
                            OngletTag.loadContent(this, conteneurAjax, href);
                            conteneurAjax.show();
                        } else {
                            if (contenu !== null) {
                                contenu.show();
                            }
                        }

                        OngletTag.select(this);
                        conteneur.set('aria-busy', false);

                    }

                }

            });

            // On selectionne le premier onglet ou celui indiqué dans les parametres
            if (selected && selection === null) {
                selection = element.get('id');
            }

        });

        if (selection) {
            $(selection).fireEvent('click');
        } else {
            $(elements[0]).fireEvent('click');
        }

    }

}

/*
 *  On etend l'objet OngletTag afin qu'il prenne en charge le chargement d'onglets internes
 */
OngletTag.extend({
    /**
     * Fonction controllant l'URL courant afin d'afficher le bon onglet
     */
    catchUrl: function () {
        var anchor = window.location.hash;
        anchor = anchor.substring(1, anchor.length);

        if (anchor && $(anchor)) {
            OngletTag.loadInternalContent($$("a[role=tab][data-content=" + anchor + "]"), $(anchor));
        }
    }
});

/*
 *
 * Graphiques
 *
 */

/*
 *  Création d'un histogramme
 */

function phpk_graphes(element, type) {

    var type = type || 'simple';

    if ("undefined" !== typeof Graphe) {

        var tableau = $(element + '_donnees');
        var graphique = $(element + '_graphique');

        // Traitement des données
        if (tableau) {

            var caption = tableau.getChildren('table>caption').get('text');
            var width = graphique.get('aria-width');
            var height = graphique.get('aria-height');
            var legend = graphique.get('aria-legend');
            var xAxis = '';
            var yAxis = '';
            var categories = [];
            var series = [];

            switch (type) {

                case "complexe":

                    yAxis = tableau.getChildren('table>tbody>tr>th')[0].get('text');
                    xAxis = tableau.getChildren('table>tbody>tr:nth-child(2)>th')[0].get('text');

                    tableau.getChildren('table>tbody>tr:nth-child(n+3)').each(function (item, i) {
                        categories[i] = item.getChildren('th')[0].get('text');
                    });

                    tableau.getChildren('table>tbody>tr:nth-child(2)>th:not(:first-child)').map(function (item, i) {
                        series[i] = {name: item.get('text'), data: []};
                    });

                    tableau.getChildren('table>tbody>tr:nth-child(n+3)').map(function (item, i) {
                        item.getChildren('td').each(function (col, j) {
                            series[j].data.push(col.get('data-value').toFloat());
                        });
                    });

                    break;

                case "simple":
                default:

                    xAxis = tableau.getChildren('table>tbody>tr>th')[0].get('text');
                    yAxis = tableau.getChildren('table>tbody>tr>th')[1].get('text');

                    categories = [];

                    var data = tableau.getChildren('table>tbody>tr:not(:first-child)');
                    series = data.map(function (item, i) {
                        var name = item.getChildren('td')[0].get('text');
                        var value = Array.from(item.getChildren('td')[1].get('data-value').toFloat());
                        return {name: name, data: value};
                    });

                    break;
            }

            if (series.length > 0) {

                // Affichage du graphique
                window[ element + '_graphique_data'] = {
                    "xAxis": xAxis,
                    "yAxis": yAxis,
                    "categories": categories,
                    "series": series
                };
                Graphe.init($(element + '_graphique'), {
                    "width": width,
                    "height": height,
                    "title": caption,
                    "legend": legend,
                    "pattern": "#.##"
                });

            }

        }

    } else {
        $(element).set("html", "<p class='zone-alerte'>Attention, la bibliothèque Graphe n'est pas charg�e</p>");
    }

}

/**
 * Fonction permettant d'ajouter une option à un select
 * @param element comboTarget : select où l'on veut que l'option soit injectée
 * @param data : objet contenant les attributs text et value
 */
combosTag.addItem = function (comboTarget, data) {
    if (data) {
        new Element('option', {value: data.value, text: data.text}).inject(comboTarget);
        comboTarget.fireEvent("liszt:updated");
    }
};

/**
 * Fonction permettant de supprimer une option d'un select
 * @param element comboTarget : select où l'on veut que l'option soit supprimée
 * @param value : valeur de l'option qui doit être supprimée
 */
combosTag.removeItem = function (comboTarget, value) {
    comboTarget.getElements('option').each(function (element) {
        if (element.getProperty('value') == value) {
            element.destroy();
        }
    });
    comboTarget.fireEvent("liszt:updated");
};

/**
 * Fonction utilisée lors de l'export d'un tableau
 * @param element
 * @param name
 */
function phpk_table_export(element, name) {
    var form = $(element).getParent('form');
    var input = $('phpk_table_export_hidden');

    if (form && input) {
        input.set('name', name);
        form.submit();
        input.set('name', '');
    }
}

/**
 * colorpicker
 */

var ColorPickerTag = new Class({
    Implements: [Options],
    options: {
        defaultColor: '',
        colorsPerLine: 8,
        changeOnHover: false,
        prefix: 'colorpicker',
        colors: ['#000000', '#444444', '#666666', '#999999', '#cccccc', '#eeeeee', '#f3f3f3', '#ffffff'
                    , '#ff0000', '#ff9900', '#ffff00', '#00ff00', '#00ffff', '#0000ff', '#9900ff', '#ff00ff'
                    , '#f4cccc', '#fce5cd', '#fff2cc', '#d9ead3', '#d0e0e3', '#cfe2f3', '#d9d2e9', '#ead1dc'
                    , '#ea9999', '#f9cb9c', '#ffe599', '#b6d7a8', '#a2c4c9', '#9fc5e8', '#b4a7d6', '#d5a6bd'
                    , '#e06666', '#f6b26b', '#ffd966', '#93c47d', '#76a5af', '#6fa8dc', '#8e7cc3', '#c27ba0'
                    , '#cc0000', '#e69138', '#f1c232', '#6aa84f', '#45818e', '#3d85c6', '#674ea7', '#a64d79'
                    , '#990000', '#b45f06', '#bf9000', '#38761d', '#134f5c', '#0b5394', '#351c75', '#741b47'
                    , '#660000', '#783f04', '#7f6000', '#274e13', '#0c343d', '#073763', '#20124d', '#4C1130']
    },
    element: null,
    box: null,
    input: null,
    link: null,
    initialize: function (element, options) {
        this.setOptions(options);
        this.label = $$('label[for=' + element + ']');
        this.element = document.id(element);
        // If prefix is not set, prefix is element id
        if (!options.prefix) {
            this.options.prefix = element;
        }
        // Create element
        return this.build();
    },
    build: function () {

        var self = this;

        // Build colorbox
        this.box = new Element('div', {
            'class': 'colorpicker-box',
            id: this.options.prefix + '-colorbox',
            events: {
                mouseleave: function () {
                    if (self.options.changeOnHover === true) {
                        self.setDefaultColor();
                    }
                }
            }
        });
        var colorBoxColors = new Element('ul');

        // Build color selection
        Array.each(this.options.colors, function (currentColor, i) {

            currentColor = currentColor.toUpperCase();

            var colorUnit = new Element('li', {
                styles: {
                    'background-color': currentColor
                },
                'class': 'colorpicker-color',
                title: currentColor,
                id: self.options.prefix + '-color-' + i,
                'data-color': currentColor,
                events: {
                    click: function () {
                        self.selectColor(currentColor);
                    },
                    mouseover: function () {
                        if (self.options.changeOnHover === true) {
                            self.hoverColor(currentColor);
                        }
                    }
                }
            });

            if (i % self.options.colorsPerLine === 0) {
                colorUnit.setStyle('clear', 'both');
            }

            colorUnit.inject(colorBoxColors);
        });

        // Build color input
        this.input = new Element('div', {
            'class': 'colorpicker-input',
            styles: {
                'background-color': this.options.defaultColor
            },
            'data-color': this.options.defaultColor,
            'title': this.options.defaultColor,
            events: {
                click: function () {
                    self.positionAndShowBox();
                }
            }
        });

        // Build Icon
        this.link = new Element('a', {
            'class': 'colorpicker-link',
            events: {
                click: function () {
                    self.positionAndShowBox();
                }
            }
        });

        // Check label existence
        if (this.label) {
            // Add specific class
            this.label.addClass('colorpicker-label');
            // Clic event
            this.label.addEvent('click', function (e) {
                self.input.fireEvent('click', e);
            });
        }

        // Initialize default color
        this.element.set('value', this.options.defaultColor);
        this.setDefaultColor();

        // Onblur event
        $$('body').addEvent('click', function (e) {
            if (self.box.isVisible()) {
                if (!$(e.target).hasClass('colorpicker-color')
                        && !$(e.target).hasClass('colorpicker-label')
                        && !$(e.target).hasClass('colorpicker-input')
                        && !$(e.target).hasClass('colorpicker-link')) {
                    self.setDefaultColor();
                    self.hide(self.box);
                }
            }
        });

        // Place elements
        this.input.inject(this.element, 'after');
        colorBoxColors.inject(this.box);
        this.box.inject(this.input, 'after');

        this.link.inject(this.input, 'after');

        // Hide the colorbox
        this.hide(this.box);
        this.hide(this.element);

        return this;
    },
    selectColor: function (color) {
        this.hide(this.box);
        this.element.set('value', color);
        this.input.set('data-color', color).set('title', color).setStyle('background-color', color);
        this.element.fireEvent('onSelectColor', color);
    },
    setDefaultColor: function () {
        var color = this.element.get('value');
        this.input.set('data-color', color)
                .set('title', color)
                .setStyle('background-color', color);
        this.element.fireEvent('onSetDefaultColor', color);
    },
    hoverColor: function (color) {
        this.input.setStyle('background-color', color);
    },
    hide: function (element) {
        element.hide();
    },
    show: function (element) {
        element.show();
    },
    positionAndShowBox: function () {
        this.box.position({
            relativeTo: this.link,
            position: 'bottomLeft',
            edge: 'upperLeft'
        });
        this.show(this.box);
    }

});

ColorPickerTag.extend({
    init: function (element, options) {
        return new ColorPickerTag(element, options);
    }
});

ZoomTag.initZoomHorsCharte = function () {

    var body = document.getElementsByTagName("body")[0];
    var palierActu = this.lireCookie();
    var zoomAgrandir = $$('.zoom-agrandir');
    var zoomReduire = $$('.zoom-reduire');

    zoomAgrandir.addEvents({
        'click': function () {
            ZoomTag.zoom();
        },
        'keydown': function (event) {
            if (event.key === 'enter') {
                ZoomTag.zoom();
            }
        }
    });
    zoomReduire.addEvents({
        'click': function () {
            ZoomTag.dezoom();
        },
        'keydown': function (event) {
            if (event.key === 'enter') {
                ZoomTag.dezoom();
            }
        }
    });

    this.toggleAriaPressed('.zoom-agrandir');
    this.toggleAriaPressed('.zoom-reduire');

    if (palierActu === null || palierActu === '' || (palierActu < this.palierinit)) {
        body.style.fontSize = this.tailleinit + this.uniteZoom;
        this.creerCookie(this.palierinit);
    } else {
        if (palierActu > this.palierinit + this.nbzoom) {
            body.style.fontSize = (this.tailleinit + (this.nbzoom * this.pourcentzoom)) + this.uniteZoom;
            this.creerCookie(this.palierinit + this.nbzoom);
        } else {
            body.style.fontSize = (((parseInt(palierActu) - 1) * this.pourcentzoom) + this.tailleinit) + this.uniteZoom;
        }
    }
}

/**
 * Étend Chosen afin de permettre de créer des select multiple avec autocompletion en Ajax
 *
 * Les options pour cette méthode sont les suivantes :
 *  url : indique l'url à utiliser pour la requête Ajax
 *  query : indique le nom du paramètre à utiliser dans la requête Ajax
 *
 * Le retour doit être au format suivant :
 *
 *  [{
 *  	"value": 1,
 *	"text": "Valeur 1",
 *	"domaine": "Groupe 1"
 *  }, {
 * 	"value": 2,
 *	"text": "Valeur 2",
 *	"domaine": "Groupe 1"
 *  }]
 *
 */
Chosen.Ajax = new Class({
    Extends: Chosen,
    Implements: [Options, Events],
    options: {
        url: '',
        query: ''
    },
    initialize: function (elmn, data, options) {
        this.parent(elmn);
        this.query = data.query;
        this.req = new Request.JSON({url: data.url, headers: {'X-Request': 'JSON'}});
        var that = this;
        this.req.addEvents({
            'success': function (dataa) {
                that.reqSuccess(dataa)
            }
        });
    },
    reqSuccess: function (data) {
        var items = new Array();
        var that = this;
        if (!(data != null)) {
            return;
        }

        Array.each(this.form_field.getElements('option'), function (el) {
            if (!el.get("selected")) {
                el.destroy();
            } else {
                items.push(el.get("value"));
            }
        });

        Object.each(data, function (el) {
            if( !items.contains(el.value) ){
                that.form_field.grab(new Element('option', {'value': el.value, 'html': el.text}));
            }
        });
        this.form_field.fireEvent("liszt:updated");
        this.search_field.set('value', this.search_field.get('prevVal'));
    },
    keyup_checker: function (evt) {

        var field, val;
        var term = {}

        val = evt.target.get('value');
        if (val.length < 3 || val === evt.target.get('prevVal') || val === "" || val === 0) {
            return false;
        }

        evt.target.set('prevVal', val);
        field = evt.target;
        term[this.query] = val;
        this.req.get(term);
    }
});

function phpk_user_info() {

    var lienUserInfo = $('lien-user-info');
    var divUserInfo = $('phpk-hc-user-info');
    var divUserInfoOffset = {x: 130, y: 3};

    // Gestion de la couleur du lien
    lienUserInfo.set('data-default-color', lienUserInfo.getStyle('color'));

    // On cache la div
    divUserInfo.addClass('hide');

    // Gestion du click
    lienUserInfo.addEvent('click', function () {

        if (divUserInfo.hasClass('hide')) {
            divUserInfo.position({
                relativeTo: lienUserInfo,
                position: 'bottomRight',
                edge: 'upperRight',
                offset: divUserInfoOffset
            }).removeClass('hide').fade('in');
            // Le retablissement de la couleur ne fonctionne pas sous ie8
            if (!Browser.ie8) {
                lienUserInfo.setStyle('color', '#000000');
            }
        } else {
            divUserInfo.addClass('hide').fade('out');
            if (!Browser.ie8) {
                lienUserInfo.setStyle('color', $('lien-user-info').get('data-default-color'));
            }
        }

    });

    // Gestion du onblur
    $$('body').addEvent('click', function (e) {
        if (!divUserInfo.hasClass('hide')) {
            if (!divUserInfo.contains($(e.target)) && !lienUserInfo.contains($(e.target))) {
                divUserInfo.addClass('hide').fade('out');
                if (!Browser.ie8) {
                    lienUserInfo.setStyle('color', lienUserInfo.get('data-default-color'));
                }
            }
        }
    });

    // Gestion du window resize
    $(window).addEvent('resize', function (e) {
        divUserInfo.position({
            relativeTo: lienUserInfo,
            position: 'bottomRight',
            edge: 'upperRight',
            offset: divUserInfoOffset
        });
    });

}
