--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.4
-- Dumped by pg_dump version 9.1.4
-- Started on 2012-07-18 19:46:10 MSK

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
-- Dependencies: 5
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
    rgt integer NOT NULL
);


ALTER TABLE public.atlas_category OWNER TO mk;

--
-- TOC entry 2000 (class 0 OID 34113)
-- Dependencies: 200
-- Data for Name: atlas_category; Type: TABLE DATA; Schema: public; Owner: mk
--

COPY atlas_category (id, parent_id, title, description, root, lvl, lft, rgt) FROM stdin;
11	1	Археологическое общество	\N	1	1	20	21
1	\N	Root	\N	1	0	1	80
12	1	Архитектурный	\N	1	1	22	23
40	1	Этнографический	\N	1	1	78	79
13	1	Библиотека	\N	1	1	24	25
14	1	Высшее учебное заведение	\N	1	1	26	27
5	2	Памятники	\N	1	2	3	4
15	1	Галерея	\N	1	1	28	29
16	1	Дворец культуры	\N	1	1	30	31
17	1	Детская	\N	1	1	32	33
18	1	Дом-музей	\N	1	1	34	35
19	1	Исторический	\N	1	1	36	37
20	1	Картинная галерея	\N	1	1	38	39
21	1	Кинотеатр	\N	1	1	40	41
22	1	Краеведческий	\N	1	1	42	43
23	1	Кукольный	\N	1	1	44	45
24	1	Литературное объединение	\N	1	1	46	47
25	1	Любительский	\N	1	1	48	49
9	6	Светская	\N	1	3	6	7
26	1	Музей	\N	1	1	50	51
6	2	Архитектура	\N	1	2	5	10
2	1	Объекты культурного наследия	\N	1	1	2	15
7	2	Природа	\N	1	2	11	12
3	1	Музеи	\N	1	1	16	17
4	1	Учреждения	\N	1	1	18	19
8	2	Другое	\N	1	2	13	14
10	6	Религиозная	\N	1	3	8	9
27	1	Музей-заповедник	\N	1	1	52	53
28	1	Музей-квартира	\N	1	1	54	55
29	1	Музейный комплекс	\N	1	1	56	57
30	1	Музыкальное	\N	1	1	58	59
31	1	Образовательное учреждение	\N	1	1	60	61
32	1	Союз композиторов	\N	1	1	62	63
33	1	Союз писателей	\N	1	1	64	65
34	1	Союз художников	\N	1	1	66	67
35	1	Творческий союз	\N	1	1	68	69
36	1	Театр	\N	1	1	70	71
37	1	Театр юного зрителя	\N	1	1	72	73
38	1	Филармония	\N	1	1	74	75
39	1	Центр писателя	\N	1	1	76	77
\.


--
-- TOC entry 1997 (class 2606 OID 34120)
-- Dependencies: 200 200
-- Name: atlas_category_pkey; Type: CONSTRAINT; Schema: public; Owner: mk; Tablespace: 
--

ALTER TABLE ONLY atlas_category
    ADD CONSTRAINT atlas_category_pkey PRIMARY KEY (id);


--
-- TOC entry 1998 (class 1259 OID 34121)
-- Dependencies: 200
-- Name: idx_d5dd1f6f727aca70; Type: INDEX; Schema: public; Owner: mk; Tablespace: 
--

CREATE INDEX idx_d5dd1f6f727aca70 ON atlas_category USING btree (parent_id);


--
-- TOC entry 1999 (class 2606 OID 34122)
-- Dependencies: 200 1996 200
-- Name: fk_d5dd1f6f727aca70; Type: FK CONSTRAINT; Schema: public; Owner: mk
--

ALTER TABLE ONLY atlas_category
    ADD CONSTRAINT fk_d5dd1f6f727aca70 FOREIGN KEY (parent_id) REFERENCES atlas_category(id) ON DELETE SET NULL;


-- Completed on 2012-07-18 19:46:10 MSK

--
-- PostgreSQL database dump complete
--

