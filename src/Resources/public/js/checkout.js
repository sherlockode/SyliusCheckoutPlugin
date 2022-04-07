class Checkout
{
    constructor(publicKey, tokenInputSelector, existingCardInputSelector, debug, locale) {
        Frames.init({
            publicKey: publicKey,
            debug: debug,
            localization: locale.toUpperCase().replace(/_/g, '-'),
        });

        let tokenInput = document.querySelector(tokenInputSelector),
            form = document.querySelector(tokenInputSelector);

        while (form.parentNode && form.tagName.toLowerCase() !== 'form') {
            form = form.parentNode;
        }

        form.addEventListener("submit", function (event) {
            let existingCardInput = form.querySelector(existingCardInputSelector + ':checked');
            if (existingCardInput) {
                form.submit();
            }

            event.preventDefault();
            Frames.submitCard()
                .then(function (data) {
                    Frames.addCardToken(form, data.token);
                    tokenInput.value = data.token;
                    form.submit();
                })
                .catch(function (error) {
                    form.submit();
                    if (debug) {
                        console.error(error);
                    }
                });
        });
    }
}
