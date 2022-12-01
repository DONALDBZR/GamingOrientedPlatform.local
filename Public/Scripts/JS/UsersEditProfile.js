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
        const delay = 3760;
        /**
         * Using Form-Data to upload the file
         */
        const formData = new FormData();
        for (let index = 0; index < this.state.profilePicture.length; index++) {
            formData.append("image", this.state.profilePicture[index]);
        }
        event.preventDefault();
        fetch("/Controllers/UserEditProfile.php", {
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
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.username}`}>Parkinston</a>
                    </div>
                    <div>
                        <a href={`/Users/Profile/${this.state.username}`} class="fa fa-user"></a>
                    </div>
                    <div>
                        <a href="/Sign-Out" class="fa fa-sign-out"></a>
                    </div>
                </nav>
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
    render() {
        return (
            <form method="POST" enctype="multipart/form-data" onSubmit={this.handleSubmit.bind(this)}>
                <div id="label">You can customize your profile picture</div>
                <input type="file" name="image" accept="image/*" files={this.state.profilePicture} onChange={this.handleFileChange.bind(this)} required />
                <div>
                    <button>Change Profile Picture</button>
                </div>
                <div>{this.state.message}</div>
            </form>
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
