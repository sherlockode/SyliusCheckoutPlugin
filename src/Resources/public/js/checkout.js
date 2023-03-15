export default class Checkout
{
    constructor() {
        let form = document.querySelector('form[class="checkout-payment-form"]');

        if (null === form) {
            return;
        }

        let publicKey = form.getAttribute('data-public-key'),
            locale = form.getAttribute('data-locale'),
            tokenInput = form.querySelector('input[data-contains-checkout-token="true"]'),
            existingCardInput = form.querySelector('input[data-checkout-existing-card="true"]'),
            debug = false;

        Frames.init({
            publicKey: publicKey,
            debug: debug,
            localization: locale.toUpperCase().replace(/_/g, '-'),
        });

        form.addEventListener("submit", function (event) {
            if (existingCardInput && existingCardInput.checked) {
                return;
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

        let removeCardButtons = form.querySelectorAll('button[data-remove-card]');

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
