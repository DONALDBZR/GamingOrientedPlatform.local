/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the application
         */
        this.state = {
            User: {
                username: "",
                password: "",
            },
            System: {
                url: "",
                status: 0,
                message: "",
            },
        };
    }
    /**
     * Redirecting the user to an intended url
     * @param {int} delay
     * @returns {void}
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
        }, delay);
    }
    /**
     * Handling any change that is made in the user interface
     * @param {Event} event
     * @returns {void}
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState((previous) => ({
            User: {
                ...previous.User,
                [name]: value,
            },
        }));
    }
    /**
     * Handling the form submission
     * @param {Event} event
     * @returns {void}
     */
    handleSubmit(event) {
        const delay = 2000;
        event.preventDefault();
        if (
            (this.state.User.username != null ||
                this.state.User.username != "") &&
            (this.state.User.password != null || this.state.User.password != "")
        ) {
            fetch("/Controllers/Login.php", {
                method: "POST",
                body: JSON.stringify({
                    username: this.state.User.username,
                    password: this.state.User.password,
                }),
                headers: {
                    "Content-Type": "application/json",
                },
            })
                .then((response) => response.json())
                .then((data) =>
                    this.setState({
                        System: {
                            status: data.status,
                            message: data.message,
                            url: data.url,
                        },
                    })
                )
                .then(() => this.redirector(delay));
        } else {
            this.setState({
                System: {
                    status: 1,
                    message: "Please fill in all fields",
                    url: window.location.href,
                },
            });
            this.redirector(delay);
        }
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.System.status == 0) {
            return "rgb(0%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseFontSize() {
        if (this.state.System.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
    }
    /**
     * Component that is rendered depending on the media query
     * @returns {Application} Component
     */
    headerDivA_Register() {
        if (window.innerWidth <= 340) {
            return (
                <a href="/Register">
                    <span class="fa fa-sign-in"></span>
                </a>
            );
        } else {
            return <a href="/Register">Register</a>;
        }
    }
    /**
     * Renders the components that are being returned
     * @returns {Application} Component
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    render() {
        return (
            <header>
                <div>
                    <a href="/">Parkinston</a>
                </div>
                <div>{this.headerDivA_Register()}</div>
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
                <div>
                    <img src="/Public/Images/istockphoto-1175691444-612x612.jpg" />
                </div>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="label">Login Form</div>
                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        value={this.state.User.username}
                        onChange={this.handleChange.bind(this)}
                        required
                    />
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        value={this.state.User.password}
                        onChange={this.handleChange.bind(this)}
                        required
                    />
                    <div id="button">
                        <button>Login</button>
                    </div>
                    <div id="response">
                        <h1
                            style={{
                                color: this.handleResponseColor(),
                                fontSize: this.handleResponseFontSize(),
                            }}
                        >
                            {this.state.System.message}
                        </h1>
                    </div>
                </form>
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return (
            <footer>
                <div>
                    You have forgotten your password? Reset it{" "}
                    <a href="/ForgotPassword">Here</a>!
                </div>
                <div>Parkinston</div>
            </footer>
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
