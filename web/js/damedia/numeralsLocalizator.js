//TODO: Instance "Localizer" with current locale on "new_base.html.twig" template load. For instance: Localizer = new Localizer(currentLocale);

var DamediaNumeralsLocalizator = (function(){
    var localizator = {},

        arrayOne = [0, 5, 6, 7, 8, 9],
        arrayTwo = [11, 12, 13, 14, 15, 16, 17, 18, 19],

        numberIsInArrayOne = function(number){
            return ($.inArray(parseInt((number + '').substr(-1)), arrayOne) !== -1);
        },
        numberIsInArrayTwo = function(number){
            return ($.inArray(parseInt((number + '').substr(-2)), arrayTwo) !== -1);
        },
        penultimateDigitIsOne = function(number){
            return (parseInt((number + '').substr(-1)) === 1);
        },

        cyrillicVariants = function(number, variants){
            if (numberIsInArrayOne(number) || numberIsInArrayTwo(number)) {
                return number + ' ' + variants[0];
            }
            else if (penultimateDigitIsOne(number)) {
                return number + ' ' + variants[1];
            }
            else {
                return number + ' ' + variants[2];
            }
        },
        latinVariants = function(number, variants){
            if (number === 1) {
                return number + ' ' + variants[0];
            }
            else {
                return number + ' ' + variants[1];
            }
        };

    localizator.localizeDays = function(number, locale){
        switch (locale) {
            case 'ru':
                return cyrillicVariants(number, ['дней', 'день', 'дня']);
                break;

            case 'en':
                return latinVariants(number, ['day', 'days']);
                break;

            default:
                console.error('Unknown locale (locale = ' + locale + ')!');
                return '';
        }
    };

    localizator.localizeHours = function(number, locale){
        switch (locale) {
            case 'ru':
                return cyrillicVariants(number, ['часов', 'час', 'часа']);
                break;

            case 'en':
                return latinVariants(number, ['hour', 'hours']);
                break;

            default:
                console.error('Unknown locale (locale = ' + locale + ')!');
                return '';
        }
    };

    localizator.localizeMinutes = function(number, locale){
        switch (locale) {
            case 'ru':
                return cyrillicVariants(number, ['минут', 'минута', 'минуты']);
                break;

            case 'en':
                return latinVariants(number, ['minute', 'minutes']);
                break;

            default:
                console.error('Unknown locale (locale = ' + locale + ')!');
                return '';
        }
    };

    return localizator;
}());