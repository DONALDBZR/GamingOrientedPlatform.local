/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
    }
    /**
     * Renders the components that are being returned
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
    /**
     * Redirecting the user to an intended url
     * @param {int} delay
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = ServerRendering.state.url;
        }, delay);
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    render() {
        return (
            <header>
                <a href="/">Parkinston</a>
            </header>
        );
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <main>
                <Form />
            </main>
        );
    }
}
/**
 * The component that is the form
 */
class Form extends Main {
    constructor(props) {
        super(props);
        /**
         * States of the component
         */
        this.state = {
            /**
             * Username of the user
             * @type {string}
             */
            username: "",
            /**
             * Mail address of the user
             * @type {string}
             */
            mailAddress: "",
        };
    }
    /**
     * Handling any change that is made in the user interface
     * @param {Event} event
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState({
            [name]: value,
        });
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 2000;
        event.preventDefault();
        fetch("/Controllers/Register.php", {
            method: "POST",
            body: JSON.stringify({
                username: this.state.username,
                mailAddress: this.state.mailAddress,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                ServerRendering.setState({
                    status: data.status,
                    message: data.message,
                    url: data.url,
                })
            )
            .then(() => super.redirector(delay));
    }
    render() {
        return (
            <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                <div id="label">Registration Form</div>
                <input
                    type="text"
                    name="username"
                    placeholder="Username"
                    value={this.state.username}
                    onChange={this.handleChange.bind(this)}
                    required
                />
                <input
                    type="mail"
                    name="mailAddress"
                    placeholder="Mail Address"
                    value={this.state.mailAddress}
                    onChange={this.handleChange.bind(this)}
                    required
                />
                <div id="button">
                    <button>Register</button>
                </div>
                <ServerRendering />
            </form>
        );
    }
}
/**
 * The component that is taking the response from the POST request
 */
class ServerRendering extends Form {
    constructor(props) {
        super(props);
        /**
         * States of the component
         */
        this.state = {
            /**
             * The status returned from the request
             * @type {int}
             */
            status: 0,
            /**
             * The message that will be displayed to the user
             * @type {string}
             */
            message: "",
            /**
             * The url to be redirected after displaying the message
             * @type {string}
             */
            url: "",
        };
    }
    render() {
        return (
            <div id="response">
                <h1>{this.state.message}</h1>
            </div>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
