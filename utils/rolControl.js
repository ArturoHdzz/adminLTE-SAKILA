function useComponent(roles) {
    const userData = JSON.parse(localStorage.getItem('user_data'));
    if (!userData || !userData.user || !userData.user.role) {
        return false;
    }
    const userRole = userData.user.role.role_name.toLowerCase();
    return roles.includes(userRole);
}
