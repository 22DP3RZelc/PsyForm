import React from "react";
import { useNavigate } from "react-router-dom";
import API from "../api";

const Dashboard = () => {
    const navigate = useNavigate();

    const handleLogout = async () => {
        await API.post("/logout", {}, { headers: { Authorization: `Bearer ${localStorage.getItem("token")}` } });
        localStorage.removeItem("token");
        navigate("/login");
    };

    return (
        <div>
            <h2>Dashboard</h2>
            <button onClick={handleLogout}>Logout</button>
        </div>
    );
};

export default Dashboard;
