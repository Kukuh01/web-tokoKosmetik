import React, { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import {  z} from "zod";
import { viewBookingSchema } from "../types/validationBooking";
import apiClient from "../services/apiServices";
import { isAxiosError } from "axios";

export default function MyOrdersPage(){

    const [formData, setFormData] = useState({
        email: "",
        booking_trx_id: "",
    });

    const [formErrors, setFormErrors] = useState<z.ZodError["issues"]>([]);
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();

    // Handle form input changes
    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value} = e.target;
        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    // Handle form submission and validation
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        // Validate form data using zod
        const validation = viewBookingSchema.safeParse(formData);
        if(!validation.success){
            setFormErrors(validation.error.issues);
            return;
        }

        // Clear any existing errors
        setFormErrors([]);
        setLoading(true);

        try {
            const response = await apiClient.post("/check-booking", formData);

            if(response.status === 200 && response.data.data){
                // Navi
                navigate("/my-booking", { state: { bookingDetails: response.data.data, notFound: false}});
            } else {
                navigate("/my-booking", {state: { bookingDetails: null, notFound: true}});
            }
        } catch(err){
            if(isAxiosError(err)){
                if(err.response && err.response.status === 404){
                    navigate("/my-booking", {state: {bookingDetails: null, notFound: true}});
                }
            }
        } finally {
            setLoading(false);
        }
    };

    return (
<main className="relative mx-auto flex min-h-screen max-w-[640px] flex-col gap-5 bg-[#F6F6F8] pb-[162px]">
  <div id="Background" className="absolute top-0 left-0 right-0">
    <img
      src="/assets/images/backgorunds/cosmetic-equipment.png"
      alt="image"
      className="h-[421px] w-full object-cover object-bottom"
    />
  </div>
  <div className="mt-[155px] flex flex-col gap-[30px] px-5">
    <header className="relative flex flex-col gap-1">
      <img
        src="/assets/images/icons/car-black.svg"
        alt="icon"
        className="mx-auto size-[70px] shrink-0"
      />
      <h1 className="text-center text-[26px] font-bold leading-[39px]">
        Track Your Orders
      </h1>
      <p className="text-center">
        Gunakan informasi di bawah agar anda dapat melihat status pemesanannya
      </p>
    </header>
    <form
      id="Information"
      className="relative"
      onSubmit={handleSubmit}
    >
      <div className="flex w-full flex-col gap-5 rounded-3xl bg-white px-[14px] py-5">
        <label className="flex flex-col gap-[6px]">
          <h4 className="font-semibold text-[#030504]">Booking TRX ID</h4>
          <div className="group relative flex h-[54px] items-center justify-center rounded-full bg-[#E0E0EC] transition-all duration-300 focus-within:bg-cosmetics-gradient-purple-pink">
            <input
              type="text"
              name="booking_trx_id"
              value={formData.booking_trx_id}
              onChange={handleChange}
              className="absolute h-[calc(100%_-_2px)] w-[calc(100%_-_2px)] rounded-full bg-[#F6F6F8] pl-[57px] pr-[13px] font-semibold text-[#030504] transition-all duration-300 placeholder:font-normal placeholder:leading-[24px] placeholder:text-[#ACACB9] focus:h-[calc(100%_-_4px)] focus:w-[calc(100%_-_4px)] focus:outline-none"
              placeholder="Enter your Booking ID"
            />
            <div className="absolute left-[14px] top-1/2 flex w-[35px] -translate-y-1/2 justify-between">
              <img
                src="/assets/images/icons/booking-trx-id.svg"
                alt="icon"
                className="size-[24px] shrink-0"
              />
              <span className="h-[26px] w-px bg-[#E0E0EC] transition-all duration-300 group-focus-within:bg-cosmetics-gradient-purple-pink" />
            </div>
          </div>
            {formErrors.find((error) => error.path.includes("booking_trx_id")) && (
            <p className="text-sm leading-[21px] text-[#E70011]">
              {formErrors.find((error) => error.path.includes("booking_trx_id"))?.message}
            </p>
            )}
        </label>
        <label className="flex flex-col gap-[6px]">
          <h4 className="font-semibold text-[#030504]">Email Address</h4>
          <div className="group relative flex h-[54px] items-center justify-center rounded-full bg-[#E0E0EC] transition-all duration-300 focus-within:bg-cosmetics-gradient-purple-pink">
            <input
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              className="absolute h-[calc(100%_-_2px)] w-[calc(100%_-_2px)] rounded-full bg-[#F6F6F8] pl-[57px] pr-[13px] font-semibold text-[#030504] transition-all duration-300 placeholder:font-normal placeholder:leading-[24px] placeholder:text-[#ACACB9] focus:h-[calc(100%_-_4px)] focus:w-[calc(100%_-_4px)] focus:outline-none"
              placeholder="Write your complete email"
            />
            <div className="absolute left-[14px] top-1/2 flex w-[35px] -translate-y-1/2 justify-between">
              <img
                src="/assets/images/icons/mail.svg"
                alt="icon"
                className="size-[24px] shrink-0"
              />
              <span className="h-[26px] w-px bg-[#E0E0EC] transition-all duration-300 group-focus-within:bg-cosmetics-gradient-purple-pink" />
            </div>
          </div>
            {formErrors.find((error) => error.path.includes("email")) && (
            <p className="text-sm leading-[21px] text-[#E70011]">
              {formErrors.find((error) => error.path.includes("email"))?.message}
            </p>
            )}
        </label>
        <button
        disabled={loading}
          type="submit"
          className="flex w-full justify-center rounded-full bg-cosmetics-gradient-pink-white py-[14px] font-semibold text-white transition-all duration-300 hover:shadow-[0px_6px_22px_0px_#FF4D9E82]"
        >
          {loading ? "Checking..." : "Find My Booking"}
        </button>
      </div>
    </form>
  </div>
  <nav className="fixed bottom-0 left-0 right-0 z-30 w-full mx-auto">
    <div className="mx-auto max-w-[640px]">
      <div className="h-[89px] bg-white px-[30px] shadow-[0px_-4px_30px_0px_#1107260D]">
        <ul className="flex justify-between">
          <li className="flex items-center">
            <Link to={`/`}>
              <div className="flex w-[50px] flex-col items-center gap-1">
                <img
                  src="/assets/images/icons/browse-black.svg"
                  alt="icon"
                  className="size-6 shrink-0"
                />
                <p className="text-sm leading-[21px]">Browse</p>
              </div>
            </Link>
          </li>
          <li className="flex items-center">
            <Link to={`/check-booking`}>
              <div className="flex w-[50px] flex-col items-center gap-1">
                <img
                  src="/assets/images/icons/car-pink.svg"
                  alt="icon"
                  className="size-6 shrink-0"
                />
                <p className="text-sm font-semibold leading-[21px] text-cosmetics-pink">
                  Orders
                </p>
              </div>
            </Link>
          </li>
          <li>
            <a href="" className="relative -top-[23px]">
              <div className="relative flex h-[80px] w-[80px] items-center justify-center rounded-full bg-[#F5F5F7]">
                <div className="flex size-[65px] items-center justify-center rounded-full bg-cosmetics-gradient-pink-white transition-shadow duration-300 hover:shadow-[0px_6px_10px_0px_#FF4D9E6E]">
                  <img
                    src="/assets/images/icons/video.svg"
                    alt="icon"
                    className="size-[30px] shrink-0"
                  />
                </div>
              </div>
            </a>
          </li>
          <li className="flex items-center">
            <a href="">
              <div className="flex w-[50px] flex-col items-center gap-1">
                <img
                  src="/assets/images/icons/gift.svg"
                  alt="icon"
                  className="size-6 shrink-0"
                />
                <p className="text-sm leading-[21px]">Perks</p>
              </div>
            </a>
          </li>
          <li className="flex items-center">
            <a href="">
              <div className="flex w-[50px] flex-col items-center gap-1">
                <img
                  src="/assets/images/icons/message.svg"
                  alt="icon"
                  className="size-6 shrink-0"
                />
                <p className="text-sm leading-[21px]">Helps</p>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</main>

    )
}