--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.4
-- Dumped by pg_dump version 9.1.4
-- Started on 2012-07-19 23:02:59 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 200 (class 1259 OID 34113)
-- Dependencies: 1996 5
-- Name: atlas_category; Type: TABLE; Schema: public; Owner: mk; Tablespace: 
--

CREATE TABLE atlas_category (
    id integer NOT NULL,
    parent_id integer,
    title character varying(255) NOT NULL,
    description text,
    root integer,
    lvl integer NOT NULL,
    lft integer NOT NULL,
    rgt integer NOT NULL,
    icon character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.atlas_category OWNER TO mk;

--
-- TOC entry 2001 (class 0 OID 34113)
-- Dependencies: 200
-- Data for Name: atlas_category; Type: TABLE DATA; Schema: public; Owner: mk
--

COPY atlas_category (id, parent_id, title, description, root, lvl, lft, rgt, icon) FROM stdin;
41	1	Оркестр	\N	1	1	80	81	\N
26	1	Музей	\N	1	1	56	57	pin_musem.png
44	1	Музыкальный коллектив	\N	1	1	86	87	\N
11	1	Археологическое общество	\N	1	1	40	41	pin_other.png
4	1	Учреждения	\N	1	1	38	39	pin_other.png
13	1	Библиотека	\N	1	1	42	49	pin_library.png
14	13	Высшее учебное заведение	\N	1	2	45	46	pin_science.png
39	13	Центр писателя	\N	1	2	47	48	pin_other.png
1	\N	Root	\N	1	0	1	90	\N
12	3	Архитектурный	\N	1	2	33	34	pin_other.png
27	3	Музей-заповедник	\N	1	2	31	32	pin_musem.png
3	1	Музеи	\N	1	1	16	37	pin_musem.png
28	3	Музей-квартира	\N	1	2	17	18	pin_musem.png
9	6	Светская	\N	1	3	6	7	pin_other.png
10	6	Религиозная	\N	1	3	8	9	pin_other.png
2	1	Объекты культурного наследия	\N	1	1	2	15	pin_other.png
5	2	Памятники	\N	1	2	3	4	pin_other.png
6	2	Архитектура	\N	1	2	5	10	pin_other.png
7	2	Природа	\N	1	2	11	12	pin_other.png
8	2	Другое	\N	1	2	13	14	pin_other.png
35	1	Творческий союз	\N	1	1	62	69	pin_other.png
36	1	Театр	\N	1	1	70	77	pin_theater.png
37	36	Театр юного зрителя	\N	1	2	73	74	pin_theater.png
34	35	Союз художников	\N	1	2	63	64	pin_other.png
42	1	Культурный центр	\N	1	1	82	83	\N
45	1	Цирк	\N	1	1	88	89	\N
24	1	Литературное объединение	\N	1	1	54	55	pin_other.png
20	3	Картинная галерея	\N	1	2	27	28	pin_theater.png
17	13	Детская	\N	1	2	43	44	pin_library.png
22	3	Краеведческий	\N	1	2	25	26	pin_theater.png
16	1	Дворец культуры	\N	1	1	50	51	pin_other.png
15	3	Галерея	\N	1	2	21	22	pin_other.png
21	1	Кинотеатр	\N	1	1	52	53	pin_theater.png
25	36	Любительский	\N	1	2	75	76	pin_other.png
23	36	Кукольный	\N	1	2	71	72	pin_theater.png
33	35	Союз писателей	\N	1	2	67	68	pin_other.png
31	1	Образовательное учреждение	\N	1	1	58	61	pin_science.png
30	31	Музыкальное	\N	1	2	59	60	pin_other.png
38	1	Филармония	\N	1	1	78	79	pin_other.png
32	35	Союз композиторов	\N	1	2	65	66	pin_other.png
43	1	Научно-методический центр	\N	1	1	84	85	\N
19	3	Исторический	\N	1	2	35	36	pin_musem.png
29	3	Музейный комплекс	\N	1	2	29	30	pin_musem.png
40	3	Этнографический	\N	1	2	23	24	pin_theater.png
18	3	Дом-музей	\N	1	2	19	20	pin_musem.png
\.


--
-- TOC entry 1998 (class 2606 OID 34120)
-- Dependencies: 200 200
-- Name: atlas_category_pkey; Type: CONSTRAINT; Schema: public; Owner: mk; Tablespace: 
--

ALTER TABLE ONLY atlas_category
    ADD CONSTRAINT atlas_category_pkey PRIMARY KEY (id);


--
-- TOC entry 1999 (class 1259 OID 34121)
-- Dependencies: 200
-- Name: idx_d5dd1f6f727aca70; Type: INDEX; Schema: public; Owner: mk; Tablespace: 
--

CREATE INDEX idx_d5dd1f6f727aca70 ON atlas_category USING btree (parent_id);


--
-- TOC entry 2000 (class 2606 OID 34122)
-- Dependencies: 200 200 1997
-- Name: fk_d5dd1f6f727aca70; Type: FK CONSTRAINT; Schema: public; Owner: mk
--

ALTER TABLE ONLY atlas_category
    ADD CONSTRAINT fk_d5dd1f6f727aca70 FOREIGN KEY (parent_id) REFERENCES atlas_category(id) ON DELETE SET NULL;


-- Completed on 2012-07-19 23:02:59 MSK

--
-- PostgreSQL database dump complete
--

