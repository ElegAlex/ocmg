window.addEvent('domready', function () {
    $$('.w150px').setStyle('width', '45%');

    (!profile) ? noProfile() : initProfile();

    $('bouchon_utilisateur').addEvent('change', function() {
        window.location.href = Routing.generate('phpk_bouchon', { user: $('bouchon_utilisateur').getSelected().get('value') });
    });

    $('bouchon_services').addEvent('change', function() {
        updateServiceField('diversFormulaire');
    });

    $('bouchon_diversFormulaire').addEvent('blur', function() {
        addServiceField('diversFormulaire');
    });

    $('bouchon_validate').addEvent('click', function() {
        if($('bouchon_services').length > 0) {
            $('bouchon_hiddenServices').set('value', JSON.encode(profile.services));
        }
    });
});

function noProfile() {
    $$('input[id^=bouchon_]').setProperty('disabled', true);
    toggleField('bouchon_utilisateur', true);
    toggleField('bouchon_services', true);
    toggleConfirm('bouchon_addservice');
    toggleConfirm('bouchon_deleteservice');
}

function initProfile() {
    $$('input[id^=bouchon_]').set('disabled', false);
    $('bouchon_deleteuser').set('href', Routing.generate('phpk_delete_bouchon', { profile: $('bouchon_utilisateur').getSelected().get('value') }));

    if(!profile.services) {
        // Cas de validation du formulaire KO avec maintien des services saisis JS
        if($('bouchon_hiddenServices').get('value') != "") {
            profile.services = JSON.decode($('bouchon_hiddenServices').get('value'));
            Object.each(profile.services, function(attributes, name) {
                $('bouchon_services').adopt(new Element('option', {'html': name, 'value': name}));
            });
            updateServicesChoice();
            updateServiceField('diversFormulaire');
        }
        else {
            profile.services = { };
        }
        initServices();
    }
}

function initServices() {
    if(Object.getLength(profile.services) == 0) {
        toggleField('bouchon_services', true);
        toggleField('bouchon_diversFormulaire', false);
        toggleConfirm('bouchon_deleteservice');
    }
}

function toggleConfirm(id) {
    if($(id).getFirst('span').hasClass('btn_action')) {
        $(id).getFirst('span').removeClass('btn_action').addClass('btn_inactif');
    }
    else {
        $(id).getFirst('span').removeClass('btn_inactif').addClass('btn_action');
    }
}

function toggleField(id, choice) {
    if(choice == true) {
        if($(id).getProperty('disabled') == true) {
            $(id).setProperty('disabled', false).fireEvent('liszt:updated');
        }
        else {
            $(id).setProperty('disabled', true).fireEvent('liszt:updated');
        }
    }
    else {
        if($(id).getProperty('disabled') == true) {
            $(id).setProperty('disabled', false);
        }
        else {
            $(id).setProperty('disabled', true);
        }
    }
}

function addUser(name) {
    window.location.href = Routing.generate('phpk_init_bouchon', { profile: name.toUpperCase() });
}

function addService(name) {
    initServices();

    name = name.toUpperCase();
    $('bouchon_services').adopt(new Element('option', {'html': name, 'value': name, 'selected': 'selected'}));
    updateServicesChoice();
    $('bouchon_diversFormulaire').set('value', '');

    profile.services[name] = {
        diversFormulaire: null
    };
}

function deleteService(name) {
    var name = $('bouchon_services').getSelected().get('value');
    $$('#bouchon_services option[value=' + name + ']').destroy();
    updateServicesChoice();
    updateServiceField('diversFormulaire');

    delete profile.services[name];

    initServices();
}

function updateServicesChoice() {
    $('bouchon_services_chzn').empty().dispose();
    new Chosen($('bouchon_services'), { disable_search_threshold: 1000, width: '100%' });
    $('bouchon_services').fireEvent('liszt:updated');
}

function addServiceField(field) {
    var name = $('bouchon_services').getSelected().get('value');

    profile.services[name][field] = $('bouchon_' + field).get('value');
}

function updateServiceField(field) {
    var name = $('bouchon_services').getSelected().get('value');

    if (profile.services[name]) {
        $('bouchon_' + field).set('value', profile.services[name][field]);
    }
    else {
        $('bouchon_' + field).set('value', '');
    }
}