/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the properties of the component
         */
        this.state = {
            /**
             * Username of the user
             * @type {string}
             */
            username: "",
            /**
             * Mail Address of the user
             * @type {string}
             */
            mailAddress: "",
            /**
             * Domain of the application
             * @type {string}
             */
            domain: "",
            /**
             * Response's status
             * @type {int}
             */
            status: 0,
            /**
             * Response's body
             * @type {string}
             */
            message: "",
            /**
             * Url to be redirected
             * @type {string}
             */
            url: "",
            /**
             * User's profile picture
             */
            profilePicture: "",
            array: [],
        };
        this.handleFileChange = this.handleFileChange.bind(this);
    }
    /**
     * Renders the components that are being returned
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        fetch("/Users/CurrentUser",
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((data) => this.setState({
                username: data.username,
                mailAddress: data.mailAddress,
                domain: data.domain,
                profilePicture: data.profilePicture,
            }));
    }
    /**
     * Handling any change that is made in the interface
     * @param {Event} event
     */
    handleFileChange(event) {
        this.setState({
            profilePicture: event.target.files,
            array: [],
        });
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        /**
        * The amount of milliseconds that the registration process takes
        */
        const delay = 2600;
        /**
         * Using Form-Data to upload the file
         */
        const formData = new FormData();
        for (let index = 0; index < this.state.profilePicture.length; index++) {
            formData.append("image", this.state.profilePicture[index]);
        }
        event.preventDefault();
        fetch("/Controllers/UsersEditProfile.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => this.setState({
                success: data.success,
                message: data.message,
                url: data.url,
            }))
            .then(() => this.redirector(delay));
    }
    /**
     * Redirecting the user to an intended url
     * @param {int} delay 
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.url;
        }, delay);
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <header>
                <NavigationBar />
            </header>
        );
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
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
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return (
            <footer>
                Parkinston
            </footer>
        );
    }
}
/**
 * Form component
 */
class Form extends Main {
    constructor(props) {
        super(props);
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.status == 0) {
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
        if (this.state.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
    }
    render() {
        return (
            <form method="POST" enctype="multipart/form-data" onSubmit={this.handleSubmit.bind(this)}>
                <div id="label">You can customize your profile picture</div>
                <input type="file" name="image" accept="image/*" files={this.state.profilePicture} onChange={this.handleFileChange.bind(this)} required />
                <div>
                    <button>Change Profile Picture</button>
                </div>
                <div id="response">
                    <h1
                        style={{
                            color: this.handleResponseColor(),
                            fontSize: this.handleResponseFontSize(),
                        }}
                    >
                        {this.state.message}
                    </h1>
                </div>
            </form>
        );
    }
}
/**
 * The navigation bar component
 */
class NavigationBar extends Header {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <nav>
                <div>
                    <a href={`/Users/Home/${this.state.username}`}>Parkinston</a>
                </div>
                <ProfileLink />
                <div>
                    <a href="/Sign-Out" class="fa fa-sign-out"></a>
                </div>
            </nav>
        );
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
}
/**
 * The component which will render the profile picture of the user
 */
class ProfileLink extends NavigationBar {
    constructor(props) {
        super(props);
    }
    /**
     * Verifying the state before rendering the link
     * @returns {Application} Component
     */
    verifyState() {
        if (this.state.profilePicture != null) {
            return (
                <a href={`/Users/Profile/${this.state.username}`} class="fa fa-user">
                    <img src={this.state.profilePicture} />
                </a>
            );
        } else {
            return <a href={`/Users/Profile/${this.state.username}`} class="fa fa-user"></a>
        }
    }
    render() {
        return (
            <div>{this.verifyState()}</div>
        );
    }
    componentDidMount() {
        this.retrieveData();
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
