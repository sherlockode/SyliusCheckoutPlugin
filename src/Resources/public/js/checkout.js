class Checkout
{
    constructor(publicKey, tokenInputSelector, existingCardInputSelector, debug, locale) {
        Frames.init({
            publicKey: publicKey,
            debug: debug,
            localization: locale.toUpperCase().replace(/_/g, '-'),
        });

        let tokenInput = document.querySelector(tokenInputSelector),
            form = this.getClosest(document.querySelector(tokenInputSelector), 'form'),
            removeCardButtons;

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

        removeCardButtons = form.querySelectorAll('button[data-remove-card]');

        for (let i = 0; i < removeCardButtons.length; i++) {
            removeCardButtons[i].addEventListener('click', function (event) {
                let card = this.getClosest(event.target, 'div');
                card.parentNode.removeChild(card);
                let request = new XMLHttpRequest();
                request.open('DELETE', removeCardButtons[i].getAttribute('data-remove-card'), true);
                request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                request.send();
            }.bind(this));
        }
    }

    getClosest(origin, tagName) {
        while (origin.parentNode && origin.tagName.toLowerCase() !== tagName) {
            origin = origin.parentNode;
        }

        return origin;
    }
}
