/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    /**
     * Component that is rendered depending on the media query
     * @returns {Application} Component
     */
    headerDivA_Login() {
        if (document.body.clientWidth <= 639) {
            return (
                <a href="/Login">
                    <span class="fa fa-sign-in"></span>
                </a>
            );
        } else {
            return <a href="/Login">Login</a>;
        }
    }
    /**
     * Component that is rendered depending on the media query
     * @returns {Application} Component
     */
    headerDivA_PatchNotes() {
        if (document.body.clientWidth <= 639) {
            return (
                <a href="/PatchNotes">
                    <span class="fas fa-sticky-note"></span>
                </a>
            );
        } else {
            return <a href="/PatchNotes">Patch-Notes</a>;
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
                <div>Parkinston</div>
                <div>{this.headerDivA_PatchNotes()}</div>
                <div>{this.headerDivA_Login()}</div>
            </header>
        );
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    render() {
        return (
            <main>
                <div>
                    <img src="/Public/Images/istockphoto-1175691444-612x612.jpg" />
                </div>
                <div>
                    <div>
                        Welcome to <span>Parkinston</span>
                    </div>
                    <div>
                        Our aim is to help you into analyzing your stats from
                        your journey in various competitive games as well as
                        giving you access to various resources that can be
                        helpful to you!
                    </div>
                </div>
            </main>
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
