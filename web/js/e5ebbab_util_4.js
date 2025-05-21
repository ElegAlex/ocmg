/**
 * Indique si la date est bien au format jj/mm/yyyy et est une date valide
 *
 * @param {string} date Date au format jj/mm/yyyy
 * @returns boolean
 */
function phpk_isDate(date) {
    var regexp = new RegExp("^([0-9]{2})/([0-9]{2})/([0-9]{4})$");
    var res = regexp.exec(date);

    var jour = res !== null ? parseInt(res[1], 10) : 0;
    var mois = res !== null ? parseInt(res[2], 10) : 0;
    var annee = res !== null ? parseInt(res[3], 10) : 0;

    var jourMax;

    if (mois < 8 && mois % 2 === 1 || mois > 7 && mois % 2 === 0) {
        jourMax = 31;
    }
    else if (mois === 2) {
        if (annee % 4 === 0 && annee % 100 !== 0 || annee % 400 === 0) {
            jourMax = 29;
        }
        else {
        jourMax = 28;
        }
    }
    else {
        jourMax = 30;
    }

    return jour > 0 && jour <= jourMax && mois > 0 && mois < 13;
}
